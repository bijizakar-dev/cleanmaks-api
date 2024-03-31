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

    public function applicant() {
        return $this->belongsTo(Employee::class, 'employee_id_applicant')->with('divisi');
    }

    public function replacement() {
        return $this->belongsTo(Employee::class, 'employee_id_replacement')->with('divisi');
    }

    public function user_decide() {
        return $this->belongsTo(User::class, 'user_id_decide');
    }

    public function cuti_type() {
        return $this->belongsTo(JenisType::class, 'type');
    }

    public function getListCuti($param){
        $conditions = [];

        $baseQuery = self::select('cutis.*', 'ea.name as name_applicant', 'eb.name as name_replacement', 'u.name as name_user_decide', 'jt.name as type')
                ->join('employees as ea', 'cutis.employee_id_applicant', '=', 'ea.id')
                ->join('employees as eb', 'cutis.employee_id_replacement', '=', 'eb.id')
                ->join('jenis_types as jt', 'cutis.type', '=', 'jt.id')
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

    public function detail($id) {
        $sql = "SELECT c.*,
                e.name as name_applicant,
                e2.name as name_replacement,
                d.name as divisi_applicant,
                d2.name as divisi_replacement,
                j.name as jabatan_applicant,
                j2.name as jabatan_replacement,
                e2.name as employee_decide_name,
                jt.name as type_name
                FROM cutis c
                JOIN employees e ON (c.employee_id_applicant = e.id)
                JOIN employees e2 ON (c.employee_id_replacement = e2.id)
                JOIN divisi d ON (e.unit_id = d.id)
                JOIN divisi d2 ON (e2.unit_id = d2.id)
                LEFT JOIN jabatan j ON (e.jabatan_id = j.id)
                LEFT JOIN jabatan j2 ON (e2.jabatan_id = j2.id)
                LEFT JOIN users u ON (c.user_id_decide = u.id)
                LEFT JOIN employees eu on (u.employee_id = eu.id)
                LEFT JOIN jenis_types jt on (jt.id = c.type)
                WHERE c.id = :id
            ";

        $data = DB::selectOne($sql, ['id' => $id]);

        return $data;
    }
}
