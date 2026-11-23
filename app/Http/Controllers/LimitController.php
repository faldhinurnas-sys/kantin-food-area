<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Limit;
use Illuminate\Support\Facades\Auth;

class LimitController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'daily_limit' => 'nullable|numeric|min:0',
            'category_limits' => 'nullable|array',
        ]);

        // Bersihkan nilai null atau kosong dari category_limits
        $categoryLimits = $request->category_limits ? array_filter($request->category_limits, function($val) {
            return !is_null($val) && $val !== '';
        }) : null;

        Limit::updateOrCreate(
            ['student_id' => $request->student_id, 'parent_id' => Auth::id()],
            [
                'daily_limit' => $request->daily_limit ?: null,
                'category_limits' => empty($categoryLimits) ? null : $categoryLimits
            ]
        );

        return back()->with('success', 'Limit pengeluaran berhasil diperbarui.');
    }
}
