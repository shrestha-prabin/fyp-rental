<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Auth
 */
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('/login', 'AuthController@login');
    Route::post('/register', 'AuthController@register');
});

Route::group([
    'middleware' => 'auth.role:admin,buyer,seller',
    'prefix' => 'auth'
], function () {
    Route::post('/user-profile', 'AuthController@userProfile');
    Route::post('/logout', 'AuthController@logout');
});

Route::group([
    'prefix' => 'apartment'
], function () {
    Route::post('/all-apartments', 'ApartmentController@getAllApartments');
    Route::post('/apartment-types', 'ApartmentController@getApartmentTypes');
});

Route::group([
    'middleware' => 'auth.role:seller',
    'prefix' => 'apartment'
], function () {
    Route::post('/add-apartment', 'ApartmentController@addApartment');
    Route::post('/update-apartment', 'ApartmentController@updateApartment');
    Route::post('/user-apartments', 'ApartmentController@getUserApartments');
});

Route::group([
    'middleware' => 'auth.role:admin,seller',
    'prefix' => 'apartment'
], function () {
    Route::post('/delete-apartment', 'ApartmentController@deleteApartment');
});


Route::group([
    'middleware' => 'auth.role:buyer',
    'prefix' => 'booking'
], function () {
    Route::post('/book-apartment', 'BookingController@bookApartment');
    Route::post('/user-bookings', 'BookingController@getUserBookings');
});

Route::group([
    'middleware' => 'auth.role:seller',
    'prefix' => 'booking'
], function () {
    Route::post('/booking-requests', 'BookingController@getBookingRequests');
    Route::post('/update-status', 'BookingController@updateBookingStatus');
});

Route::group([
    'middleware' => 'auth.role:buyer',
    'prefix' => 'review'
], function () {
    Route::post('/add-review', 'ReviewController@addReview');
});


Route::group([
    'middleware' => 'auth.role:admin,seller,buyer',
    'prefix' => 'message'
], function () {
    Route::post('/friend-list', 'ChatsController@getFriendList');
    Route::post('/add-friend', 'ChatsController@addFriend');
    Route::post('/get-messages', 'ChatsController@getMessages');
    Route::post('/chat-history', 'ChatsController@getChatHistory');
    Route::post('/send-message', 'ChatsController@sendMessage');
});
