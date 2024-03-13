<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'role',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // protected $appends = [
    //     'profile_photo_url',
    // ];


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getUserEmployee($id) {
        $sql = "select
                u.id,
                u.name as username,
                u.email as email,
                e.name, e.gender, e.age, e.phone, e.address,
                IFNULL(e.photo, '') as photo,
                e.unit_id, e.role_id
                from users u
                join employees e on (u.employee_id = e.id)
                where u.id = :id
                limit 1";

        $data = DB::selectOne($sql, ['id' => $id]);

        return $data;
    }
}
