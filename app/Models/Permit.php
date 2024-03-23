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
}
