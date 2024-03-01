<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeIdentification extends Model
{
    use HasFactory;

    protected $table = "type_identification_users";

    protected $fillable = [
        'name',
        'shortName'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }
}
