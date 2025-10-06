<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;


    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'invoice_for',
        'bill_to',
        'description',
        'vat_percent',
        'discount_percent',
        'subtotal',
        'net_amount',
        'branch_id',
        'created_by',
    ];

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id');
    }



}
