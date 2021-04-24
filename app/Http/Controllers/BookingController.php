<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ResponseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Accessible to buyer only
     */
    public function bookApartment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apartment_id' => 'required',
            'booking_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return ResponseModel::failed($validator->errors());
        }

        $user = Auth::user();

        Booking::create(array_merge($validator->validated(), [
            'buyer_id' => $user->id
        ]));

        return ResponseModel::success([
            'message' => 'Booking successful'
        ]);
    }

    /**
     * Accessible to buyer only
     * Get all bookings buyer has made
     */
    public function getUserBookings()
    {
        return ResponseModel::success(
            Booking::with('apartment', 'apartment.seller')
            ->where('buyer_id', Auth::user()->id)
            ->get()
        );
    }

    /**
     * Accessible to seller only
     * Get booking requests made by buyer
     */
    public function getBookingRequests(Request $request)
    {
        $user = Auth::user();

        return ResponseModel::success(
            Booking::with('apartment', 'buyer')
            ->whereHas('apartment', function ($q) use ($user) {
                $q->where('seller_id', $user->id);
            })->get()
        );

    }

    /**
     * Accessible to seller only
     * Seller updates booking status to pending, approved, pending
     */
    public function updateBookingStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseModel::failed($validator->errors());
        }

        $user = Auth::user();

        $booking = Booking::with('apartment')
            ->whereHas('apartment', function ($q) use ($user) {
                $q->where('seller_id', $user->id);
            })
            ->where('id', $request->booking_id)
            ->first();
        $booking->booking_status = $request->new_status;

        $booking->save();

        return ResponseModel::success([
            'message' => 'Status Updated'
        ]);
    }
}
