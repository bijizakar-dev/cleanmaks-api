<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'gender',
        'age',
        'phone',
        'photo',
        'unit_id',
        'role_id',
        'is_verified',
        'verified_at'
    ];

    function employeeHistory($employeeId) {
        // $query = "
        //     SELECT
        //         'Absensi' as data_source,
        //         users.name as user_name,
        //         absences.date as absence_date,
        //         absences.type as absence_type,
        //         NULL as cuti_date,
        //         NULL as cuti_start_date,
        //         NULL as cuti_end_date,
        //         NULL as cuti_type
        //     FROM
        //         users
        //     LEFT JOIN absences ON users.id = absences.user_id
        //     WHERE
        //         users.employee_id = " . (int)$employeeId . "

        //     UNION

        //     SELECT
        //         'Cuti' as data_source,
        //         users.name as user_name,
        //         NULL absence_date,
        //         NULL as absence_type,
        //         cutis.date as cuti_date,
        //         cutis.start_date as cuti_start_date,
        //         cutis.end_date as cuti_end_date,
        //         cutis.type as cuti_type
        //     FROM
        //         users
        //     LEFT JOIN cutis ON users.employee_id = cutis.employee_id_applicant
        //     WHERE
        //         users.employee_id = " . (int)$employeeId . "

        // ";

        // $result = DB::select($query);

        // return $result;


        $employeeId = (int)$employeeId;

        $result = DB::table('users')
            ->select(
                DB::raw("'Absensi' as data_source"),
                'users.name as user_name',
                'absences.date as absence_date',
                'absences.type as absence_type',
                DB::raw('NULL as cuti_date'),
                DB::raw('NULL as cuti_start_date'),
                DB::raw('NULL as cuti_end_date'),
                DB::raw('NULL as cuti_type')
            )
            ->leftJoin('absences', 'users.id', '=', 'absences.user_id')
            ->where('users.employee_id', $employeeId)
            ->unionAll(
                DB::table('users')
                    ->select(
                        DB::raw("'Cuti' as data_source"),
                        'users.name as user_name',
                        DB::raw('NULL as absence_date'),
                        DB::raw('NULL as absence_type'),
                        'cutis.date as cuti_date',
                        'cutis.start_date as cuti_start_date',
                        'cutis.end_date as cuti_end_date',
                        'cutis.type as cuti_type'
                    )
                    ->leftJoin('cutis', 'users.employee_id', '=', 'cutis.employee_id_applicant')
                    ->where('users.employee_id', $employeeId)
            )
            ->paginate(10);

        return $result;
    }
}
