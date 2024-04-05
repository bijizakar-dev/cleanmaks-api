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
        'status',
        'time_in',
        'time_out',

        'sunday_type',
        'sunday_in',
        'sunday_out',
        'sunday_total',

        'monday_type',
        'monday_in',
        'monday_out',
        'monday_total',

        'tuesday_type',
        'tuesday_in',
        'tuesday_out',
        'tuesday_total',

        'wednesday_type',
        'wednesday_in',
        'wednesday_out',
        'wednesday_total',

        'thursday_type',
        'thursday_in',
        'thursday_out',
        'thursday_total',

        'friday_type',
        'friday_in',
        'friday_out',
        'friday_total',

        'saturday_type',
        'saturday_in',
        'saturday_out',
        'saturday_total',

    ];

    public static function check_codeqr_setting() {
        $sql = "select s.code
                from settings s
                limit 1";
        $data = DB::selectOne($sql);

        return $data;
    }
}
