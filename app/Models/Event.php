<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    const STATUS_APPROVED = "approved";
    const STATUS_REJECTED = "rejected";
    const STATUS_WAITING = "waiting";

    const NO_CHECKED = "true";
    const CHECKED = "false";

    protected $fillable = [
        "title",
        "description",
        "start_date",
        "end_date",
        "main_image",
        "secondary_image",
        "third_image",
        "background_image",
        "check",
        "status",
        "user_id"
    ];

    protected $table = "events";

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isApproved()
    {
        return $this->status == Event::STATUS_APPROVED;

    }

    public function isRejected()
    {
        return $this->status == Event::STATUS_REJECTED;
    }

    public function isChecked()
    {
        return $this->check == Event::CHECKED;
    }
}
