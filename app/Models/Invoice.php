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
        'status',
    ];

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            1 => 'Unpaid',
            2 => 'Paid',
            3 => 'Cancelled'
        ];
        return $statuses[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            1 => 'warning',
            2 => 'success',
            3 => 'danger'
        ];
        return $colors[$this->status] ?? 'secondary';
    }


}
