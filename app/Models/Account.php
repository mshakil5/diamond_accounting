<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_type','account_name','account_desc','branch_id','user_type','updated_by','created_by'
    ];



    
     public function transaction()
    {
        return $this->hasMany('App\Models\Transaction');
    }
}
