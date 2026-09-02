<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    protected $table = 'addresses';

    protected $fillable = [
        'title',
        'address_first_line',
        'address_second_line',
        'address_third_line',
        'town',
        'postcode',
        'latitude',
        'longitude',
        'allowed_radius',
        'status',
        'branch_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /* Optional: scope to keep branch logic DRY */
    public function scopeForCurrentBranch($query)
    {
        return $query->where('branch_id', auth()->user()->branch_id);
    }
}