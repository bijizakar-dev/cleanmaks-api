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

    function employeeHistory($param) {

        $result = DB::table('users')
            ->select(
                DB::raw("'Absensi' as data_source"),
                'users.name as user_name',
                'absences.date as date',
                'absences.type as type',
                'absences.latitude as latitude',
                'absences.longitude as longitude',
                DB::raw("'' as start_date"),
                DB::raw("'' as end_date"),
                DB::raw("'' as status"))
            ->leftJoin('absences', 'users.id', '=', 'absences.user_id')
            ->when(!empty($param['employee_id']), function ($query) use ($param) {
                $query->where('users.employee_id', $param['employee_id']);
            })
            ->when(!empty($param['start_date']) && !empty($param['end_date']), function ($query) use ($param) {
                $query->where(function($query) use ($param) {
                    $query->whereBetween('absences.date', [$param['start_date'], $param['end_date']])
                        ->orWhereBetween('absences.date', [$param['start_date'].' 00:00:00', $param['end_date'].' 23:59:59']);
                });
            })
            ->unionAll(
                DB::table('users')
                    ->select(
                        DB::raw("'Cuti' as data_source"),
                        'users.name as user_name',
                        'cutis.date as date',
                        'cutis.type as type',
                        DB::raw("'' as latitude"),
                        DB::raw("'' as longitude"),
                        'cutis.start_date as start_date',
                        'cutis.end_date as end_date',
                        'cutis.status as status')
                    ->leftJoin('cutis', 'users.employee_id', '=', 'cutis.employee_id_applicant')
                    ->when(!empty($param['employee_id']), function ($query) use ($param) {
                        $query->where('users.employee_id', $param['employee_id']);
                    })
                    ->when(!empty($param['start_date']) && !empty($param['end_date']), function ($query) use ($param) {
                        $query->whereBetween('cutis.date', [$param['start_date'].' 00:00:00', $param['end_date'].' 23:59:59']);
                    })
            )
            ->paginate(10);


        return $result;
    }
}
