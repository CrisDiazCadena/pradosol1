<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    //HasUuids ---ID CON MODELO UUID---

    const VERIFIED_USER = '1';
    const NOT_VERIFIED_USER = '0';

    const ACTIVE_STATUS = 'active';
    const INACTIVE_STATUS = 'inactive';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $attributes = [
        'validation_status_id' => 3, // Establece el valor predeterminado aquí
    ];

    protected $fillable = [
        'identification_card',
        'name',
        'lastname',
        'password',
        'status',
        'address',
        'email',
        'phone',
        'verification_token',
        'verified',
        'type_identification_id',
    ];

    public function setNameAttribute($valor)
    {
        $this->attributes['name'] = strtolower($valor);
    }

    public function setLastNameAttribute($valor)
    {
        $this->attributes['lastname'] = strtolower($valor);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token'

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function typeIdentification()
    {
        return $this->belongsTo(TypeIdentification::class, 'type_identification_id');
    }

    public function partner()
    {
        return $this->hasOne(Partner::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function administrator()
    {
        return $this->hasOne(Administrator::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users');
    }

    public function validationStatusUser()
    {
        return $this->belongsTo(ValidationStatusUser::class, 'validation_status_id');
    }

    public function registers()
    {
        return $this->hasMany(Register::class);
    }

    public function isVerified()
    {
        return $this->verified == User::VERIFIED_USER;
    }

    public function isActive()
    {
        return $this->active == User::ACTIVE_STATUS;
    }

    public static function createVerificationToken()
    {
        return Str::random(40);
    }
}
