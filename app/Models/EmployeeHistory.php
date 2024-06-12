<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id','start_datetime','end_datetime','history_desc','branch_id','user_type','updated_by','created_by'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
