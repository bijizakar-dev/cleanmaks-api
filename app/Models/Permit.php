<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Permit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'employee_id_applicant',
        'type',
        'start_date',
        'end_date',
        'total',
        'reason',
        'image',
        'status',
        'user_id_decide',
        'verified_at'
    ];

    public function applicant() {
        return $this->belongsTo(Employee::class, 'employee_id_applicant')->with('divisi');
    }

    public function user_decide() {
        return $this->belongsTo(User::class, 'user_id_decide');
    }

    public function permit_type() {
        return $this->belongsTo(JenisType::class, 'type');
    }

    public function detail($id) {
        $sql = "SELECT p.*,
                e.name as employee_name,
                d.name as divisi_name,
                j.name as jabatan_name,
                e2.name as employee_decide_name,
                jt.name as type_name
                FROM permits p
                JOIN employees e ON (p.employee_id_applicant = e.id)
                JOIN divisi d ON (e.unit_id = d.id)
                LEFT JOIN jabatan j ON (e.jabatan_id = j.id)
                LEFT JOIN users u ON (p.user_id_decide = u.id)
                LEFT JOIN employees e2 on (u.employee_id = e2.id)
                LEFT JOIN jenis_types jt on (jt.id = p.type)
                WHERE p.id = :id
            ";

        $data = DB::selectOne($sql, ['id' => $id]);

        return $data;
    }

    public function getListPermit($param){
        $conditions = [];

        $baseQuery = self::select('permits.*', 'ea.name as name_applicant', 'u.name as name_user_decide', 'jt.name as type')
                ->join('employees as ea', 'permits.employee_id_applicant', '=', 'ea.id')
                ->join('jenis_types as jt', 'permits.type', '=', 'jt.id')
                ->leftJoin('users as u', 'u.id', '=', 'permits.user_id_decide')
                ->orderBy('permits.id');

        //PARAM SEARCH
        if (!empty($param['id'])) {
            $conditions[] = "`permits`.`id` = '".$param['id']."'";
        }
        if (!empty($param['id_employee'])) {
            $conditions[] = "`permits`.`employee_id_applicant` = '".$param['id_employee']."'";
        }
        if (!empty($param['status'])) {
            $conditions[] = "`permits`.`status` = '".$param['status']."'";
        }
        if (!empty($param['type'])) {
            $conditions[] = "`permits`.`type` = '".$param['type']."'";
        }
        if (!empty($param['start_date']) && !empty($param['end_date'])) {
            $conditions[] = "`permits`.`date` between '".$param['start_date']." 00:00:00' and '".$param['end_date']." 23:59:59'";
        }

        //
        if (!empty($conditions)) {
            $condition = implode(' AND ', $conditions);
            $baseQuery->whereRaw($condition);
        }

        return $baseQuery->paginate($param['limit'] ?? 10);
    }
}
