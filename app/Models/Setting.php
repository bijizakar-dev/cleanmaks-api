<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'logo',
        'latitude',
        'longitude',
        'type',
        'working_hour',
        'code',
        'status'
    ];

    public static function check_codeqr_setting() {
        $sql = "select s.code
                from settings s
                limit 1";
        $data = DB::selectOne($sql);

        return $data;
    }
}
