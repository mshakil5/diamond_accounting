<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liability extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'ref',
        'transaction_type',
        'payment_type',
        'amount',
        'description',
        'liability_id',
        'expense_id',
        'liability_date',
        'branch_id',
        'user_type',
        'updated_by',
        'created_by'
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

}
