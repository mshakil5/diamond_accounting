<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'period',
        'total_exc_vat',
        'unit_price',
        'total_inc_vat',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

}
