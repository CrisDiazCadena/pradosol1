<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    const VERIFIED = "true";
    const NO_VERIFIED = "false";

    protected $attributes = [
        'verified' => Beneficiary::NO_VERIFIED
    ];

    protected $fillable = [
        "name",
        "lastname",
        "type_beneficiary",
        "identification_card",
        "partner_id",
        "type_identification_id",
    ];

    protected $table = "beneficiaries";


    public function partner()
    {
        return $this->belongsTo(Partner::class, "partner_id");
    }

    public function typeIdentification()
    {
        return $this->belongsTo(TypeIdentification::class, "type_identification_id");
    }
}
