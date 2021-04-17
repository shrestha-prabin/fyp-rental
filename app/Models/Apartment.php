<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'name',
        'location',
        'purpose',
        'type',
        'description',
        'price',
        'image'
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function bookings() {
        return $this->hasMany(Booking::class, 'apartment_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'apartment_id');
    }
}
