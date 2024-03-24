<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'quota',
        'quota_used'
    ];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }
}
