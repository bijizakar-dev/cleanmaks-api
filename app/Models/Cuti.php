<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Cuti extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'employee_id_applicant',
        'employee_id_replacement',
        'type',
        'start_date',
        'end_date',
        'total',
        'reason',
        'file',
        'status',
        'user_id_decide',
        'verified_at'
    ];

    public function getListCuti($param){
        $conditions = [];

        $baseQuery = self::select('cutis.*', 'ea.name as name_applicant', 'eb.name as name_replacement', 'u.name as name_user_decide')
                ->join('employees as ea', 'cutis.employee_id_applicant', '=', 'ea.id')
                ->join('employees as eb', 'cutis.employee_id_replacement', '=', 'eb.id')
                ->leftJoin('users as u', 'u.id', '=', 'cutis.user_id_decide')
                ->orderBy('cutis.id');

        //PARAM SEARCH
        if (!empty($param['id'])) {
            $conditions[] = "`cutis`.`id` = '".$param['id']."'";
        }
        if (!empty($param['id_employee'])) {
            $conditions[] = "`cutis`.`employee_id_applicant` = '".$param['id_employee']."'";
        }
        if (!empty($param['status'])) {
            $conditions[] = "`cutis`.`status` = '".$param['status']."'";
        }
        if (!empty($param['type'])) {
            $conditions[] = "`cutis`.`type` = '".$param['type']."'";
        }
        if (!empty($param['start_date']) && !empty($param['end_date'])) {
            $conditions[] = "`cutis`.`date` between '".$param['start_date']." 00:00:00' and '".$param['end_date']." 23:59:59'";
        }

        //
        if (!empty($conditions)) {
            $condition = implode(' AND ', $conditions);
            $baseQuery->whereRaw($condition);
        }

        return $baseQuery->paginate($param['limit'] ?? 10);
    }
}
