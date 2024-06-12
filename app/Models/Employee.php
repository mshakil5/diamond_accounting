<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_name','employee_id','email','employee_phone','employee_address','branch_id','user_type','updated_by','created_by'
    ];
    public function employeehistory()
    {
        return $this->hasMany('App\Models\EmployeeHistory');
    }
    
    
    public function transaction()
    {
        return $this->hasMany('App\Models\Transaction');
    }
}
