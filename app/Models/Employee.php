<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = "employees";


    protected $fillable = [
        "position",
        "time_in",
        "time_out",
        "time_in",
        "start_work",
        "user_id",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
