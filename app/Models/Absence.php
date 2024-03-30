<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Absence extends Model
{
    use HasFactory;

    protected $table = 'absences';

    // Tambahkan ini untuk menonaktifkan soft deletes
    protected $dates = [];

    protected $fillable = [
        'date',
        'latitude',
        'longitude',
        'address',
        'image',
        'type',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function last_absence_user($id) {
        if(!$id) {
            return false;
        }

        $sql = "select a.*
                from absences a
                join users u on (a.user_id = u.id)
                join employees e on (u.employee_id = e.id)
                where u.id = :id
                order by a.id desc limit 1 ";

        $data = DB::selectOne($sql, ['id' => $id]);

        return $data;
    }
}
