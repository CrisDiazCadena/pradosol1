<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicensesType extends Model
{
    use HasFactory;

    protected $table = "licenses_types";

    protected $fillable = [
        'name',
        'description'
    ];

    public function employeeLicenses()
    {
        return $this->hasMany(EmployeeLicense::class);
    }
}
