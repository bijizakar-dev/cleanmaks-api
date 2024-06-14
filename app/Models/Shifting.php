<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Shifting extends Model
{
    use HasFactory;

    protected $table = 'shifting';
    protected $fillable = [
        'name',
        'time_start',
        'time_end',
        'time_diff',
        'status'
    ];
}
