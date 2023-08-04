<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function register(Request $request)
    {
       /* return response([
            'message' => 'Response'
        ],200);
        */

        $request->validate([
          'name'=>'required',
          'email'=>'required|email',
          'password'=>'required|confirmed',
          'condition'=>'required',

        ]);

        if(User::where('email', $request->email)->first())
        {
            return response([
                'message' => 'Email already registered',
                'status' => 'failed',
            ],200);
        }

        $user= User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'condition'=>json_decode($request->condition), //json object ---> php array/orbjec === json decode , (jason obj, assoc)
        ]);

        //generating token ......................

        $token = $user->createToken($request->email)->plainTextToken;

        return response([
            'token'=>$token,
            'message' => 'user registration completed',
            'status' => 'success'
        ],201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password'=> 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if($user && Hash::check($request->password, $user->password)) {

            $token = $user->createToken($request->email)->plainTextToken;

            return response([
                'token'=>$token,
                'message' => 'login success',
                'status' => 'success'
            ],200);
        }

        return response([
            'message' => 'Email or password is not correct',
            'status' => 'failed'
        ], 401);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response([
            'message' => 'Logout done',
            'status' => 'success'
        ],200);
    }

    public function active_user()
    {
        $active_user = auth()->user();

        return response([
            'user'=>$active_user,
            'message'=>$active_user->name.' is active on the site',
            'status'=>'active'
        ],200);
    }

    public function change_pass(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed',
        ]);

        $active_user=auth()->user();
        $active_user->password=Hash::make($request->password);
        $active_user->save();

        return response([
            'user'=>'user name: '.$active_user->name,
            'message' => 'Password change successfully',
            'status' => 'success'
        ],200);
    }
}
