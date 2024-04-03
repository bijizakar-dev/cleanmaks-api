<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAbsenceLog extends Model
{
    use HasFactory;

    protected $table = 'employee_absences_log';
    protected $fillable = [
        'date',
        'user_id',
        'employee_absences_id',
        'absence',
        'type',
        'device_info',
    ];
}
