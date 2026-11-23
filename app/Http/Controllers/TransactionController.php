<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Limit;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function storeOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $pedagang = Auth::user();
        $totalAmount = 0;
        $validItems = [];

        foreach ($request->items as $item) {
            $menu = Menu::find($item['menu_id']);
            if (!$menu || !$menu->is_available) {
                return back()->with('error', 'Menu tidak tersedia.');
            }
            $itemTotal = $menu->price * $item['quantity'];
            $totalAmount += $itemTotal;
            $validItems[] = [
                'menu_id' => $menu->id,
                'quantity' => $item['quantity'],
                'price' => $menu->price
            ];
        }

        $transaction = Transaction::create([
            'pedagang_id' => $pedagang->id,
            'amount' => $totalAmount,
            'type' => 'payment',
            'status' => 'pending',
            'order_status' => 'menunggu',
            'description' => 'Pesanan Baru'
        ]);

        foreach ($validItems as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        return back()->with('success', 'Pesanan berhasil disimpan.');
    }

    public function processNfc(Request $request)
    {
        $request->validate([
            'card_uid' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $pedagang = Auth::user();
        $card = Card::where('card_uid', $request->card_uid)->where('status', 'active')->first();

        if (!$card) {
            return $this->jsonOrBack($request, false, 'Kartu tidak terdaftar atau tidak aktif (UID: ' . $request->card_uid . '). Silakan daftarkan di menu Admin.');
        }

        // Hitung total belanja & kumpulkan kategori
        $totalAmount = 0;
        $categorySpends = []; // Untuk melacak pengeluaran per kategori di transaksi ini
        $validItems = [];

        foreach ($request->items as $item) {
            $menu = Menu::find($item['menu_id']);
            if (!$menu || !$menu->is_available) {
                return $this->jsonOrBack($request, false, 'Menu ' . ($menu ? $menu->name : 'tidak ditemukan') . ' tidak tersedia.');
            }

            $itemTotal = $menu->price * $item['quantity'];
            $totalAmount += $itemTotal;

            if ($menu->category) {
                if (!isset($categorySpends[$menu->category])) {
                    $categorySpends[$menu->category] = 0;
                }
                $categorySpends[$menu->category] += $itemTotal;
            }

            $validItems[] = [
                'menu_id' => $menu->id,
                'quantity' => $item['quantity'],
                'price' => $menu->price
            ];
        }

        // Cek Saldo
        if ($card->balance < $totalAmount) {
            $this->logTransaction($card->id, $pedagang->id, $totalAmount, 'failed', 'Saldo tidak mencukupi', null, $validItems);
            return $this->jsonOrBack($request, false, 'Transaksi gagal: Saldo tidak mencukupi. (Butuh Rp ' . number_format($totalAmount, 0, ',', '.') . ')');
        }

        // Cek Limit
        $limit = Limit::where('student_id', $card->user_id)->first();
        if ($limit) {
            // Daily Limit
            if ($limit->daily_limit) {
                $todaySpent = Transaction::where('card_id', $card->id)
                    ->where('status', 'success')
                    ->where('type', 'payment')
                    ->whereDate('created_at', Carbon::today())
                    ->sum('amount');
                
                if (($todaySpent + $totalAmount) > $limit->daily_limit) {
                    $this->logTransaction($card->id, $pedagang->id, $totalAmount, 'failed', 'Melebihi limit harian', null, $validItems);
                    return $this->jsonOrBack($request, false, 'Transaksi gagal: Melebihi limit harian yang diizinkan wali.');
                }
            }

            // Category Limit
            if ($limit->category_limits && is_array($limit->category_limits)) {
                foreach ($categorySpends as $category => $amountToSpend) {
                    if (isset($limit->category_limits[$category])) {
                        $maxCategory = $limit->category_limits[$category];
                        
                        // Hitung pengeluaran kategori ini hari ini
                        $todayCategorySpent = TransactionItem::whereHas('transaction', function($q) use ($card) {
                                $q->where('card_id', $card->id)
                                  ->where('status', 'success')
                                  ->where('type', 'payment')
                                  ->whereDate('created_at', Carbon::today());
                            })
                            ->whereHas('menu', function($q) use ($category) {
                                $q->where('category', $category);
                            })
                            ->select(DB::raw('SUM(price * quantity) as total'))
                            ->value('total') ?? 0;

                        if (($todayCategorySpent + $amountToSpend) > $maxCategory) {
                            $this->logTransaction($card->id, $pedagang->id, $totalAmount, 'failed', "Melebihi limit kategori: $category", null, $validItems);
                            return $this->jsonOrBack($request, false, "Transaksi gagal: Pembelian melebihi limit harian untuk kategori $category.");
                        }
                    }
                }
            }
        }

        // Proses Pembayaran
        DB::transaction(function () use ($card, $totalAmount, $pedagang, $validItems) {
            $card->balance -= $totalAmount;
            $card->save();

            $this->logTransaction($card->id, $pedagang->id, $totalAmount, 'success', 'Pembayaran Kantin', 'nfc', $validItems);
        });

        return $this->jsonOrBack($request, true, 'Transaksi berhasil senilai Rp ' . number_format($totalAmount, 0, ',', '.'));
    }

    public function payOrder(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)->where('status', 'pending')->firstOrFail();
        
        $request->validate([
            'payment_method' => 'required|in:tunai,nfc,midtrans',
            'card_uid' => 'required_if:payment_method,nfc'
        ]);

        if ($request->payment_method === 'tunai') {
            $transaction->update([
                'status' => 'success',
                'payment_method' => 'tunai',
                'order_status' => 'diproses'
            ]);
            
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Pembayaran tunai berhasil']);
            }
            return back()->with('success', 'Pembayaran tunai berhasil.');
        }

        if ($request->payment_method === 'nfc') {
            $card = Card::where('card_uid', $request->card_uid)->where('status', 'active')->first();
            if (!$card) {
                return $this->jsonOrBack($request, false, 'Kartu tidak terdaftar atau tidak aktif (UID: ' . $request->card_uid . '). Silakan daftarkan di menu Admin.');
            }

            // Hitung kategori
            $categorySpends = [];
            foreach ($transaction->items as $item) {
                $menu = $item->menu;
                if ($menu && $menu->category) {
                    if (!isset($categorySpends[$menu->category])) {
                        $categorySpends[$menu->category] = 0;
                    }
                    $categorySpends[$menu->category] += ($item->price * $item->quantity);
                }
            }

            // Cek Saldo
            if ($card->balance < $transaction->amount) {
                return $this->jsonOrBack($request, false, 'Saldo tidak mencukupi.');
            }

            // Cek Limit
            $limit = Limit::where('student_id', $card->user_id)->first();
            if ($limit) {
                if ($limit->daily_limit) {
                    $todaySpent = Transaction::where('card_id', $card->id)
                        ->where('status', 'success')
                        ->where('type', 'payment')
                        ->whereDate('created_at', Carbon::today())
                        ->sum('amount');
                    
                    if (($todaySpent + $transaction->amount) > $limit->daily_limit) {
                        return $this->jsonOrBack($request, false, 'Melebihi limit harian.');
                    }
                }

                if ($limit->category_limits && is_array($limit->category_limits)) {
                    foreach ($categorySpends as $category => $amountToSpend) {
                        if (isset($limit->category_limits[$category])) {
                            $maxCategory = $limit->category_limits[$category];
                            $todayCategorySpent = TransactionItem::whereHas('transaction', function($q) use ($card) {
                                    $q->where('card_id', $card->id)->where('status', 'success')
                                      ->whereDate('created_at', Carbon::today());
                                })
                                ->whereHas('menu', function($q) use ($category) {
                                    $q->where('category', $category);
                                })->select(DB::raw('SUM(price * quantity) as total'))->value('total') ?? 0;

                            if (($todayCategorySpent + $amountToSpend) > $maxCategory) {
                                return $this->jsonOrBack($request, false, "Melebihi limit kategori $category.");
                            }
                        }
                    }
                }
            }

            DB::transaction(function () use ($card, $transaction) {
                $card->balance -= $transaction->amount;
                $card->save();

                $transaction->update([
                    'status' => 'success',
                    'payment_method' => 'nfc',
                    'card_id' => $card->id,
                    'order_status' => 'diproses'
                ]);
            });

            return $this->jsonOrBack($request, true, 'Pembayaran NFC berhasil.');
        }

        if ($request->payment_method === 'midtrans') {
            // Handle Midtrans via ajax
            try {
                \Midtrans\Config::$serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
                \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', true));
                \Midtrans\Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', env('MIDTRANS_IS_SANITIZED', true));
                \Midtrans\Config::$is3ds = (bool) config('services.midtrans.is_3ds', env('MIDTRANS_IS_3DS', true));

                $orderId = 'PAY-' . time() . '-' . $transaction->id;
                
                $transaction->update([
                    'order_id' => $orderId,
                    'payment_method' => 'midtrans'
                ]);

                $params = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => (int) $transaction->amount,
                    ],
                    'customer_details' => [
                        'first_name' => 'Pelanggan',
                        'last_name' => 'Kantin',
                    ],
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $transaction->update(['snap_token' => $snapToken]);

                return response()->json(['success' => true, 'snap_token' => $snapToken]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }

    public function checkBalance(Request $request)
    {
        $request->validate([
            'card_uid' => 'required|string',
        ]);

        $cardUid = trim(strtoupper($request->card_uid));
        $card = Card::where('card_uid', $cardUid)->with('user')->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu NFC dengan UID [' . $cardUid . '] tidak terdaftar dalam sistem.'
            ], 404);
        }

        $user = $card->user;
        $limit = Limit::where('student_id', $card->user_id)->first();
        $todaySpent = Transaction::where('card_id', $card->id)
            ->where('status', 'success')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        $remainingDaily = null;
        if ($limit && $limit->daily_limit) {
            $remainingDaily = max(0, $limit->daily_limit - $todaySpent);
        }

        return response()->json([
            'success' => true,
            'card_uid' => $card->card_uid,
            'status' => $card->status,
            'balance' => (int) $card->balance,
            'balance_formatted' => 'Rp ' . number_format($card->balance, 0, ',', '.'),
            'user_name' => $user ? $user->name : 'Mahasiswa',
            'user_email' => $user ? $user->email : '-',
            'user_avatar' => $user ? $user->avatar_url : null,
            'daily_limit' => $limit && $limit->daily_limit ? 'Rp ' . number_format($limit->daily_limit, 0, ',', '.') : 'Tanpa Limit',
            'remaining_daily_limit' => $remainingDaily !== null ? 'Rp ' . number_format($remainingDaily, 0, ',', '.') : 'Bebas',
            'today_spent' => 'Rp ' . number_format($todaySpent, 0, ',', '.')
        ]);
    }

    private function jsonOrBack($request, $success, $message) {
        if ($request->wantsJson()) {
            return response()->json(['success' => $success, 'message' => $message]);
        }
        return $success ? back()->with('success', $message) : back()->with('error', $message);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('pedagang_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'order_status' => 'required|in:diproses,selesai,batal'
        ]);

        $transaction->update([
            'order_status' => $request->order_status
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui menjadi ' . strtoupper($request->order_status));
    }

    private function logTransaction($cardId, $pedagangId, $amount, $status, $desc, $paymentMethod = 'nfc', $items = [], $orderStatus = 'diproses')
    {
        $transaction = Transaction::create([
            'card_id' => $cardId,
            'pedagang_id' => $pedagangId,
            'amount' => $amount,
            'type' => 'payment',
            'payment_method' => $paymentMethod,
            'status' => $status,
            'order_status' => $status === 'success' ? $orderStatus : 'batal',
            'description' => $desc
        ]);

        if (!empty($items)) {
            foreach ($items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }
        }
    }
}
