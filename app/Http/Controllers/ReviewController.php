<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ResponseModel;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function addReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apartment_id' => 'required',
            'review_text' => 'required',
            'rating' => 'required|int|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return ResponseModel::failed($validator->errors());
        }

        $user = Auth::user();

        $review = Review::where('apartment_id', $request->apartment_id)
            ->where('buyer_id', $user->id)->first();

        if ($review) {
            $review->review_text = $request->review_text;
            $review->rating = $request->rating;

            return ResponseModel::success([
                'message' => 'Review updated'
            ]);
        }

        Review::create(array_merge($validator->validated(), [
            'buyer_id' => $user->id
        ]));

        return ResponseModel::success([
            'message' => 'Review added'
        ]);
    }
}
