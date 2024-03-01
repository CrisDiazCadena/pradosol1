<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLicense extends Model
{
    use HasFactory;

    const ACCEPTED_LICENSE = 'accepted';
    const REJECTED_LICENSE = 'rejected';
    const DEFAULT_LICENSE = 'process';

    protected $attributes = [
        'verified' => EmployeeLicense::DEFAULT_LICENSE
    ];

    protected $fillable = [
        'description',
        'support',
        'start_license',
        'end_license',
        'type_license_id',
        'employee_id',
        'comments'
    ];

    protected $table = "employee_licenses";

    public function employee()
    {
        return $this->belongsTo(Employee::class, "employee_id");
    }

    public function typeLicense()
    {
        return $this->belongsTo(LicensesType::class, "type_license_id");
    }
}
