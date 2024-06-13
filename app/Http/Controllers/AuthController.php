<?php

namespace App\Http\Controllers;

use App\Models\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Constants::RESOURCE_BAD_REQUEST_STATUS);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('passportToken')->accessToken;

            return response()->json([
                'status' => Constants::RESOURCE_OK_STATUS,
                'message' => 'Success',
                'description' => 'User has been logged successfully.',
                'data' => $user,
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'status' => Constants::RESOURCE_UNAUTHORIZED_STATUS,
            'message' => 'Unauthorized',
            'description' => 'You must be authenticated to access this resource. Please provide valid authentication credentials and try again.',
        ], Constants::RESOURCE_UNAUTHORIZED_STATUS);
    }
}
