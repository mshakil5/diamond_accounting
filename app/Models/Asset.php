<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;


    protected $fillable = [
        'asset_date',
        'account_id',
        'ref',
        'amount',
        'transaction_type',
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
