<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'date', 'note', 'branch_id', 'status', 'created_by', 'updated_by'
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;



    // Scope for strict branch isolation
    public function scopeForCurrentBranch(Builder $query)
    {
        return $query->where('branch_id', auth()->user()->branch_id);
    }
}