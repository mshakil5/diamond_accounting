<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    public function staff()
    {
        return $this->hasMany('App\Models\Staff');
    }

    public function admin()
    {
        return $this->hasMany('App\Models\Admin');
    }

    protected $fillable = [
        'branch_name','branch_phone','branch_address','updated_by','created_by'
    ];
}
