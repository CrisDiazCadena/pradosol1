<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    const TYPE_PRODUCT = "products";
    const TYPE_SERVICE = "services";

    const ACTIVE_STATUS = 'active';
    const SOLD_STATUS = 'sold out';
    const OFFER_STATUS = 'offer';

    protected $fillable = [
        'name',
        'type',
        'image',
        'price',
        'stock',
        'status',
        'description',
        'discount_price',
    ];

    public function setNameAttribute($valor)
    {
        $this->attributes['name'] = strtolower($valor);
    }

    public function setDescriptionAttribute($valor)
    {
        $this->attributes['description'] = strtolower($valor);
    }


}
