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
        'address',
        'photo',
        'unit_id',
        'role_id',
        'jabatan_id',
        'is_verified',
        'verified_at'
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'unit_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    function employeeHistory($param) {

        $result = DB::table('users')
            ->select(
                DB::raw("'Absensi' as data_source"),
                'users.name as user_name',
                'absences.date as date',
                'absences.type as type',
                'absences.latitude as latitude',
                'absences.longitude as longitude',
                'absences.image as image',
                'absences.address as address',
                DB::raw("'' as start_date"),
                DB::raw("'' as end_date"),
                DB::raw("'' as reason"),
                DB::raw("'' as status"),
                DB::raw("'' as total"))
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
                        DB::raw("IFNULL(cutis.file, '') as image"),
                        DB::raw("'' as address"),
                        'cutis.start_date as start_date',
                        'cutis.end_date as end_date',
                        'cutis.reason as reason',
                        'cutis.status as status',
                        DB::raw("IFNULL(cutis.total, '') as total"))
                    ->leftJoin('cutis', 'users.employee_id', '=', 'cutis.employee_id_applicant')
                    ->when(!empty($param['employee_id']), function ($query) use ($param) {
                        $query->where('users.employee_id', $param['employee_id']);
                    })
                    ->when(!empty($param['start_date']) && !empty($param['end_date']), function ($query) use ($param) {
                        $query->whereBetween('cutis.date', [$param['start_date'].' 00:00:00', $param['end_date'].' 23:59:59']);
                    })
            )
            ->unionAll(
                DB::table('users')
                    ->select(
                        DB::raw("'Izin' as data_source"),
                        'users.name as user_name',
                        'permits.date as date',
                        'permits.type as type',
                        DB::raw("'' as latitude"),
                        DB::raw("'' as longitude"),
                        DB::raw("IFNULL(permits.image, '') as image"),
                        DB::raw("'' as address"),
                        'permits.start_date as start_date',
                        'permits.end_date as end_date',
                        'permits.reason as reason',
                        'permits.status as status',
                        DB::raw("IFNULL(permits.total, '') as total"))
                    ->leftJoin('permits', 'users.employee_id', '=', 'permits.employee_id_applicant')
                    ->when(!empty($param['employee_id']), function ($query) use ($param) {
                        $query->where('users.employee_id', $param['employee_id']);
                    })
                    ->when(!empty($param['start_date']) && !empty($param['end_date']), function ($query) use ($param) {
                        $query->whereBetween('permits.date', [$param['start_date'].' 00:00:00', $param['end_date'].' 23:59:59']);
                    })
            )
            ->orderBy('date', 'desc')
            ->paginate(10);


        return $result;
    }
}
