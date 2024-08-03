<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        if(Auth::guard('api')->attempt($credentials)){
            $user = Auth::guard('api')->user();
            $jwt = JWTAuth::attempt($credentials);
            $success = true;
            
            return compact('success', 'user', 'jwt');
            
        }else{
            $success = false;
            $message = 'Credenciales incorrectas';
            return compact('success', 'message');
        }
    }

    public function logout(){
        Auth::guard('api')->logout();
        $success = true;
        return compact('success');
    }

    public function register(Request $request){
        $this->validator($request->all())->validate();
        event(new Registered($user = $this->create($request->all())));
        $credentials = $request->only('email', 'password');

        $jwt = JWTAuth::attempt($credentials);
        $success = true;
            
        return compact('success', 'user', 'jwt');

    }

    protected function validator(array $data)
    {
        return Validator::make($data, User::$rules);
    }

    protected function create(array $data)
    {
        return User::createPatient($data);
    }
}
