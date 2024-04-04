<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function last_absence_user($id) {
        if(!$id) {
            return false;
        }

        $sql = "select a.*
                from employee_absences a
                join users u on (a.user_id = u.id)
                join employees e on (u.employee_id = e.id)
                where u.id = :id
                order by a.id desc limit 1 ";

        $data = DB::selectOne($sql, ['id' => $id]);

        return $data;
    }
}
