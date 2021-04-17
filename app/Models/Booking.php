<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'apartment_id',
        'booking_date',
        'is_approved'
    ];

    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function apartment() {
        return$this->belongsTo(Apartment::class, 'apartment_id');
    }
}
