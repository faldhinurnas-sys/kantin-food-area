<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Limit extends Model
{
    protected $fillable = ['student_id', 'parent_id', 'daily_limit', 'per_transaction_limit', 'category_limits'];

    protected $casts = [
        'category_limits' => 'array',
    ];
}
