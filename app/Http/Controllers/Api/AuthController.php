<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api')->only('logout');
    }

    public function register(Request $request){

        $request ->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        $status = 'user';

        $user = new User([
            "name" => $request->get('name'),
            "status" => $status,
            "email" => $request->get('email'),
            "password" => bcrypt($request->get('password'))
        ]);
        $user->save();
        return response()->json([
            'success' => true
        ]);
    }

    public function login(Request $request){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',

        ]);

        $user = User::where('email',$request->get('email'))->first();

        if($user && Hash::check($request->get('password'), $user->password)){
            return response()->json([
                'success' => true,
                'user_id' => $user->id,
                'name' => $user->name,
                'status' => $user->status
            ]);
        }

       return response()->json([
           'errors' => [
              'error' => 'Такого пользователя не существует или не верный пароль'
           ],
            'status' => 422

        ]);
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->save();
        return response()->json([
            'success' => true
        ]);
    }


}
