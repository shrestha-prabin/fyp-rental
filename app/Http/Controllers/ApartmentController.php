<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ResponseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApartmentController extends Controller
{
    public function __construct()
    {
    }

    public function addApartment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'location' => 'required',
            'type' => 'required',
            'purpose' => 'required',
            'description' => 'required',
            'price' => 'required|int',
            'bhk' => 'required|int',
            'image' => 'required|mimes:jpeg'
        ]);

        if ($validator->fails()) {
            return ResponseModel::failed($validator->errors());
        }

        $user = Auth::user();
        $imagePath = $this->saveFile($request->file('image'), $request->name);

        Apartment::create(array_merge(
            $validator->validated(),
            [
                'seller_id' => $user->id,
                'image' => $imagePath
            ]
        ));

        return ResponseModel::success([
            'message' => 'Added Successfully'
        ]);
    }

    public function updateApartment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apartment_id' => 'required',
            'name' => 'required',
            'location' => 'required',
            'type' => 'required',
            'purpose' => 'required',
            'description' => 'required',
            'price' => 'required|int',
            'bhk' => 'required|int',
            'image' => 'required|mimes:jpeg'
        ]);

        if ($validator->fails()) {
            return ResponseModel::failed($validator->errors());
        }

        $apartment = Apartment::find($request->apartment_id);

        $imagePath = $this->saveFile($request->file('image'), $request->name);

        $apartment->name = $request->name;
        $apartment->location = $request->location;
        $apartment->type = $request->type;
        $apartment->purpose = $request->purpose;
        $apartment->description = $request->description;
        $apartment->price = $request->price;
        $apartment->image = $imagePath;

        $apartment->save();

        return ResponseModel::success([
            'message' => 'Updated Successfully'
        ]);
    }


    public function getAllApartments(Request $request)
    {
        $type = $request->type;
        $price_from = $request->price_from;
        $price_to = $request->price_to;
        $location = $request->location;
        $bhk = $request->bhk;

        $query = Apartment::with('seller:id,name,email,contact,address', 'reviews');

        if ($type && $type != '') {
            $query = $query->where('type', $type);
        }

        if ($price_from && $price_to) {
            $query->whereBetween('price', [$price_from, $price_to]);
        } else if ($price_from) {
            $query->where('price', '>=', $price_from);
        } else if ($price_to) {
            $query->where('price', '<=', $price_to);
        }

        if ($location) {
            $query->where('location', 'LIKE', "%{$location}%");
        }
        if ($bhk) {
            $query->where('bhk', $bhk);
        }


        $data = $query
            ->get()
            ->map(function ($apartment) {
                $apartment['image'] = Storage::url($apartment['image']);

                $reviews = $apartment->reviews;
                $total_rating = $reviews->reduce(function ($carry, $item) {
                    return $carry + $item->rating;
                }, 0);

                if ($reviews->count() > 0)
                    $apartment->rating = $total_rating / count($reviews);
                else
                    $apartment->rating = 0;

                return $apartment;
            });
        return ResponseModel::success($data);
    }

    public function getUserApartments(Request $request)
    {
        $user = Auth::user();

        return ResponseModel::success(
            Apartment::where('seller_id', $user->id)
                ->with('bookings', 'reviews')
                ->get()
                ->map(function ($apartment) {
                    $apartment['image'] = Storage::url($apartment['image']);
                    return $apartment;
                })
        );
    }

    private function saveFile($file, $name)
    {
        $fileName = time() . '-' .
            str_replace('/', '-',  str_replace(' ', '-', str_replace('.', '-', str_replace(',', '', strtolower($name)))))
            . '.jpg';

        Storage::disk('public')->put($fileName, file_get_contents($file));
        return $fileName;
    }

    public function getApartmentTypes(Request $request)
    {
        return Apartment::select('type')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return $item->type;
            });
    }

    public function getApartmentDetails(Request $request)
    {
        $details = Apartment::with('seller', 'bookings', 'reviews')->where('id', $request->id)->first();
        if ($details) {
            return ResponseModel::success(
                $details
            );
        } else {
            return ResponseModel::failed([
                'message' => 'Not Found'
            ]);
        }
    }

    public function deleteApartment(Request $request)
    {
        Apartment::find($request->apartment_id)->delete();
        return ResponseModel::success([
            'message' => 'Successfully Deleted'
        ]);
    }
}
