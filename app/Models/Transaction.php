<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;


    protected $fillable = [
        'account_id',
        'table_type',
        'ref',
        'description',
        't_date',
        'amount',
        't_rate',
        't_amount',
        'at_amount',
        'transaction_type',
        'payment_type',
        'employee_id',
        'asset_id',
        'liability_id',
        'expense_id',
        'branch_id',
        'user_type',
        'updated_by',
        'created_by',
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }
    
        public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }

}
