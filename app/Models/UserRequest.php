<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    use HasFactory;

    const STATUS_CREATED = 'created';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    const TYPE_USER = 'user';
    const TYPE_EVENT = 'event';
    const TYPE_CANCEL = 'cancel';
    const TYPE_BUG = 'bug';
    const TYPE_OTHER = 'other';

    protected $attributes = [
        'status' => UserRequest::STATUS_CREATED, // Establece el valor predeterminado aquí
    ];

    protected $fillable = [
        'type',
        'title',
        'description',
        'user_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Administrator::class, 'admin_id');
    }
}


