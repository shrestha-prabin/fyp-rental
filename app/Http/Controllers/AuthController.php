<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\ResponseModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseModel::failed($validator->errors());
        }

        if (!$token = Auth::attempt($validator->validated())) {
            return ResponseModel::failed([
                'message' => 'Invalid email or password'
            ]);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'dob' => 'required|date',
            'address' => 'string',
            'contact' => 'string',
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseModel::failed($validator->errors());
        }

        $user = User::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password)
            ]
        ));

        return ResponseModel::success([
            'message' => 'User successfully registered',
            'user' => $user
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();

        return ResponseModel::success([
            'message' => 'User successfully signed out',
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(Auth::refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return ResponseModel::success(
            Auth::user()
        );
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return ResponseModel::success([
            'message' => 'Login Successful',
            'access_token' => $token,
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => Auth::user()
        ]);
    }

    public function getUserList(Request $request)
    {
        return ResponseModel::success(
            User::all()
        );
    }

    public function deleteUser(Request $request)
    {
        $userId = $request->user_id;

        User::findorfail($userId)->delete();

        return ResponseModel::success([
            'message' => 'User Deleted'
        ]);
    }
}
