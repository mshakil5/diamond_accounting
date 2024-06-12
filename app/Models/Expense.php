<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_date',
        'account_id',
        'ref',
        'amount',
        'tax_rate',
        'tax_amount',
        'after_tax_amount',
        'payment_type',
        'description',
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
