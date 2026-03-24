<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
   protected $fillable = [
        'date', 
        'description', 
        'amount', 
        'business', 
        'category', 
        'transaction_type', 
        'source', 
        'status'
    ];
}
