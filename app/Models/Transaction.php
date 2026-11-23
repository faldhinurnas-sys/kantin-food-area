<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'card_id', 'pedagang_id', 'amount', 'type', 'payment_method', 'order_id', 'snap_token', 'status', 'order_status', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pedagang()
    {
        return $this->belongsTo(User::class, 'pedagang_id');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
