<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use HasFactory;
    protected $table = 'employee_schedule';

    protected $fillable = [
        'employee_id',
        'date',
        'day',
        'time_start',
        'time_end',
        'time_diff',
        'status'
    ];

    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
