<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'reason',
        'file',
        'status',
        'user_id_decide',
        'verified_at'
    ];

}
