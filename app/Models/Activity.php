<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
    ];

    protected $table = "activities";


    public function registers()
    {
        return $this->hasOne(Register::class);
    }
}
