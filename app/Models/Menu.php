<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['pedagang_id', 'name', 'description', 'category', 'price', 'image', 'is_available'];
}
