<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class entranceTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "identification_card",
        "partner_id",
        "type_identification_id",
    ];

    protected $table = "entrance_tickets";

    public function partner()
    {
        return $this->belongsTo(Partner::class, "partner_id");
    }

    public function typeIdentification()
    {
        return $this->belongsTo(TypeIdentification::class, "type_identification_id");
    }
}
