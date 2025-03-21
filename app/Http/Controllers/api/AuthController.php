<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    //register
    public function register(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if($validatedData->fails()){
            return response()->json([
                'status' => false,
                'message'=> 'validation error',
                'errors' => $validatedData->errors()
            ],422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
         ]);
         return response()->json([
            'status' => true,
            'message'=> 'success',
            'token'=>$user->createToken("API TOKEN")->plainTextToken
        ],200);

    }

    // login
    public function login(Request $request){
        $validatedData = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
            'password' => 'required|string|min:8',
        ]);
        $user=User::where('email',$request->email)->first();
        if(!$user || !Hash::check( $request->password,$user->password)){
            return response()->json([
                'message'=>"The provided credentials are incorrect"
            ]);
        }
        $token=$user->createToken($user->name);
        return response()->json([
            'message'=>"Login successfully",
            'user'=>$user,
            'token'=>$token->plainTextToken,
        ]);
    }

    // logout
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return [
            'message'=>"You are logged out",
        ];
    }
}
