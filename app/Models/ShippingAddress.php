<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'city',
        'postcode'
    ];

    public function formattedAddress()
    {
        return sprintf('%s, %s, %s',
            $this->address,
            $this->city,
            $this->postcode
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
