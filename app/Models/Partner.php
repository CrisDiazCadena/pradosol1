<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;


    const BONDING_PENSION = 'pensioner';
    const BONDING_WORK = 'worker';
    const BONDING_GENERAL = 'general';
    const NO_BONDING = 'none';

    const SINGLE_STATUS = 'single';
    const MARRIED_STATUS = 'married';
    const WIDOW_STATUS = 'widow';
    const SEPARATED_STATUS = 'separated';
    const LIVING_STATUS = 'living';

    protected $attributes = [
        'bonding' => Partner::NO_BONDING,
        'children' => 0,
        'marital_status' => Partner::SINGLE_STATUS

        // Establece el valor predeterminado aquí
    ];

    protected $fillable = [
        "id",
        "bonding",
        "pass",
        "children",
        "marital_status",
        "user_id"
    ];

    protected $table = "partners";


    public function isLinked()
    {
        return $this->bonding != Partner::NO_BONDING;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
