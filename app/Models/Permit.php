<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
