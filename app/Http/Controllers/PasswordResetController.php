<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Models\User;
Use Illuminate\Support\Facades\Mail;
Use Illuminate\Support\Facades\Hash;
Use Illuminate\Mail\Message;
Use Illuminate\Support\str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function reset_pass_email(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        //check exist email

        $user = User::where('email', $email )->first();

        if(!$user)
        {
            return response([
                'message'=>'Email doesnt exist',
                'status'=>'failed'
            ],400);
        }

        //generate reset token...................

        $token = str::random(60);

        //saving new password = reset password

        PasswordReset::create([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        /*

         dump("http://127.0.0.1:3000/api/user/reset/" . $token);

         return response([
            'message' => 'dump check',
            'status' => 'success'
        ],200);
        */


        //table name = password_resets ==> email, token, created_at

        //send email link = ip:port/route + token



       //dd("http://127.0.0.1:3000/api/user/reset/" . $token);

        //send link to email with reset password view

        //use($request->email), email ke mail function zeta anonymous setate pathate "use" bebohar kora hoyeche
        //less secure app pass= bsujkkoyiccaaxgs for gmail



        Mail::send('reset', ['token'=>$token], function(Message $message)use($email ){
            $message->subject('Reset Your Password');
            $message->to($email);
        });

        return response([
            'message' => 'Email send to reset your password from RIMONS LAB...please check your email',
            'status' => 'success'
        ],200);

    }



    public function reset_pass_with_token(Request $request, $token)
    {
        //delete old token
        $delet_token = Carbon::now()->subMinutes(1)->toDateTimeString();

        PasswordReset::Where('created_at', '<=', $delet_token)->delete();

        $request->validate([
            'password'=>'required|confirmed',
        ]);

        $password_reset = PasswordReset::where('token', $token)->first();

        if(!$password_reset)
        {
           return response([
                'message' => 'Token is expired',
                'status' => 'failed'
           ],400);
        }

        $user = User::where('email', $password_reset->email)->first();

        $user->password = Hash::make($request->password);

        $user->save();

        //if password is reseted then delete the token

        PasswordReset::where('email', $user->email)->delete();

        return response([
            'message'=>'Password reseted successfully for : '.$user->email,
            'status' => 'success'
        ],200);
    }

}
