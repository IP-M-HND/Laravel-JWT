<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'success'=>true,
            'user' => $user
        ],200);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => 'el usuario o contraseña son requeridos'
            ], 422);
        }

        $credentials = $request->only('email','password');
        $token = JWTAuth::attempt($credentials);
        if($token){
            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => User::where('email',$credentials['email'])->get()->first()
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'errors' => 'usuario o contraseña no validos'
            ], 422);
        }
    }

    public function refreshToken(){
        $token = JWTAuth::getToken();
        try {
            $token = JWTAuth::refresh($token);
            return response()->json([
                'success' => true,
                'token' => $token
            ], 200);
        }catch (TokenExpiredException $e){
            return response()->json([
                'success' => false,
                'errors' => 'token expiro'
            ], 422);
        }catch (TokenBlacklistedException $e){
            return response()->json([
                'success' => false,
                'errors' => 'token blacklist'
            ], 422);
        }
    }

    public function logout(){
        $token = JWTAuth::getToken();
        try {
            JWTAuth::invalidate($token);
            return response()->json([
                'success' => true,
            ], 200);
        }catch (JWTException $e){
            return response()->json([
                'success' => false,
                'errors' => 'fallo, intenta nuevamente'
            ], 422);
        }
    }

    public function profile(){
        return response()->json(auth()->user());
    }
}
