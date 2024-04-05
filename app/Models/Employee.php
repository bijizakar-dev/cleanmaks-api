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

    public function employeeCuti()
    {
        return $this->hasOne(EmployeeCuti::class);
    }

    function employeeHistory($param) {
        $result = DB::table('users')
            ->select(
                DB::raw("'Absensi' as data_source"),
                'users.name as user_name',
                'employee_absences_log.date as date',
                'employee_absences_log.absence as type',
                DB::raw("'' as image"),
                DB::raw("'' as start_date"),
                DB::raw("'' as end_date"),
                DB::raw("'' as reason"),
                DB::raw("'' as status"),
                DB::raw("'' as total"))
            ->leftJoin('employee_absences_log', 'employee_absences_log.user_id', '=', 'users.id')
            ->when(!empty($param['employee_id']), function ($query) use ($param) {
                $query->where('users.employee_id', $param['employee_id']);
            })
            ->when(!empty($param['start_date']) && !empty($param['end_date']), function ($query) use ($param) {
                $query->where(function($query) use ($param) {
                    $query->whereBetween('employee_absences_log.date', [$param['start_date'], $param['end_date']])
                        ->orWhereBetween('employee_absences_log.date', [$param['start_date'].' 00:00:00', $param['end_date'].' 23:59:59']);
                });
            })
            ->unionAll(
                DB::table('users')
                    ->select(
                        DB::raw("'Cuti' as data_source"),
                        'users.name as user_name',
                        'cutis.date as date',
                        'jenis_types.name as type',
                        DB::raw("IFNULL(cutis.file, '') as image"),
                        'cutis.start_date as start_date',
                        'cutis.end_date as end_date',
                        'cutis.reason as reason',
                        'cutis.status as status',
                        DB::raw("IFNULL(cutis.total, '') as total"))
                    ->leftJoin('cutis', 'users.employee_id', '=', 'cutis.employee_id_applicant')
                    ->leftJoin('jenis_types', 'cutis.type', '=', 'jenis_types.id')
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
                        'jenis_types.name as type',
                        DB::raw("IFNULL(permits.image, '') as image"),
                        'permits.start_date as start_date',
                        'permits.end_date as end_date',
                        'permits.reason as reason',
                        'permits.status as status',
                        DB::raw("IFNULL(permits.total, '') as total"))
                    ->leftJoin('permits', 'users.employee_id', '=', 'permits.employee_id_applicant')
                    ->leftJoin('jenis_types', 'permits.type', '=', 'jenis_types.id')
                    ->when(!empty($param['employee_id']), function ($query) use ($param) {
                        $query->where('users.employee_id', $param['employee_id']);
                    })
                    ->when(!empty($param['start_date']) && !empty($param['end_date']), function ($query) use ($param) {
                        $query->whereBetween('permits.date', [$param['start_date'].' 00:00:00', $param['end_date'].' 23:59:59']);
                    })
            )
            ->orderBy('date', 'desc')
            // ->toSql();
            ->paginate(10);


        return $result;
    }

    public static function employeeStatusCount($employee_id) {
        $cutiCounts = Cuti::where('employee_id_applicant', $employee_id)
                        ->selectRaw('status, COUNT(*) as count')
                        ->groupBy('status')
                        ->pluck('count', 'status')
                        ->toArray();

        $izinCounts = Permit::where('employee_id_applicant', $employee_id)
                        ->selectRaw('status, COUNT(*) as count')
                        ->groupBy('status')
                        ->pluck('count', 'status')
                        ->toArray();

        $combinedCounts = [];
        foreach (['Submitted', 'Pending', 'Approved', 'Rejected', 'Cancelled'] as $status) {
            $combinedCounts[strtolower($status)] = ($cutiCounts[$status] ?? 0) + ($izinCounts[$status] ?? 0);
        }

        return $combinedCounts;
    }
}
