<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\UserRole');
    }

    protected $fillable = [
        'branch_id','staff_name','staff_phone','role_id','staff_address','updated_by','created_by'
    ];
}
