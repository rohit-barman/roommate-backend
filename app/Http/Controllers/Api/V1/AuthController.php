<?php

namespace App\Http\Controllers\API\V1;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function register (Request $request) {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'mobile_number' => [
                'required',
                'regex:/^\+?\d{1,4}[-. ]?\d{1,14}$/',
                'unique:users,mobile_number'
            ],
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        

        $password = Str::random(12); // generate random password
        $user = User::create([
            'name' => $request->input('name'),
            'email' =>  $request->input('email'),
            'password' => bcrypt($password),
            'mobile_number' => $request->input('mobile_number')
        ]);

        if ($user) {
            $token = $user->createToken('auth_token')->accessToken;
            return response()->json([
                'status' => true,
                'token' => $token,
                'user' => $user,
            ], Response::HTTP_CREATED); 
        }else{
            return response()->json([
                'status' => false,
                'token' => null,
                'user' => null,
                'message' => 'failed to create user',
            ], Response::HTTP_NOT_ACCEPTABLE); 
        }
    }
}
