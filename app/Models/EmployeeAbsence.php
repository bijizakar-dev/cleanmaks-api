<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAbsence extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'user_id',
        'employee_id',
        'clock_in',
        'location_in',
        'latitude_longitude_in',
        'image_in',
        'clock_out',
        'location_out',
        'latitude_longitude_out',
        'image_out',
        'total_hour',
        'schedule',
        'status',
    ];
}
