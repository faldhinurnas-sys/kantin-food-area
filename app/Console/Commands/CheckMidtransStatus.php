<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Card;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckMidtransStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'midtrans:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check pending Midtrans transactions and update status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get transactions that are pending for more than 5 minutes and less than 1 day
        $pendingTransactions = Transaction::where('payment_method', 'midtrans')
            ->where('status', 'pending')
            ->whereNotNull('order_id')
            ->where('created_at', '<', Carbon::now()->subMinutes(5))
            ->where('created_at', '>', Carbon::now()->subDay())
            ->get();

        if ($pendingTransactions->isEmpty()) {
            $this->info('No pending Midtrans transactions found.');
            return;
        }

        \Midtrans\Config::$serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', true));

        foreach ($pendingTransactions as $transaction) {
            try {
                $statusResponse = \Midtrans\Transaction::status($transaction->order_id);

                if (is_array($statusResponse)) {
                    $statusResponse = (object) $statusResponse;
                }

                $transactionStatus = $statusResponse->transaction_status ?? null;
                $fraudStatus = $statusResponse->fraud_status ?? null;

                if (!$transactionStatus) continue;

                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'challenge') {
                        // Pending, do nothing
                    } else if ($fraudStatus == 'accept') {
                        $this->successTransaction($transaction);
                    }
                } else if ($transactionStatus == 'settlement') {
                    $this->successTransaction($transaction);
                } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                    $transaction->update(['status' => 'failed', 'order_status' => 'batal']);
                    $this->info("Transaction {$transaction->order_id} failed/expired.");
                }

            } catch (\Exception $e) {
                Log::error("Failed checking Midtrans status for {$transaction->order_id}: " . $e->getMessage());
                $this->error("Failed checking {$transaction->order_id}");
            }
        }
    }

    private function successTransaction($transaction)
    {
        if ($transaction->status == 'success') return;

        DB::transaction(function () use ($transaction) {
            $orderStatus = $transaction->type == 'topup' ? 'selesai' : 'diproses';

            $transaction->update([
                'status' => 'success',
                'order_status' => $orderStatus
            ]);

            if ($transaction->type == 'topup' && $transaction->card_id) {
                $card = Card::find($transaction->card_id);
                if ($card) {
                    $card->balance += $transaction->amount;
                    $card->save();
                }
            }
            $this->info("Transaction {$transaction->order_id} success.");
        });
    }
}
