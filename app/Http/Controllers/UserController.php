<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use Exception;

class UserController extends Controller
{
    public function signup(UserRequest $request)
    {
        try
        {
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']);

        $user = [
            'name' => $validated['name'],
            'info' => 'Press the Following Link to Verify Email',
            'Verification_link'=>url('api/verifyEmail/'.$validated['email'])
        ];

        \Mail::to($request->email)->send(new \App\Mail\NewMail($user));

         User::create($validated);
        $message = "Sign up successful";
        return response()->success($message,200);
         }
         catch(Exception $e)
         {
             return response()->error($e->getMessage(),400);
         }
    }
    public function login(LoginRequest $request)
    {
        try
        {
        $validate = $request->validated();
        $user = User::where("email",$validate["email"])->first();

        if(empty($user))
        {
            throw new Exception("No such User Exist");
        }

        if(Hash::check($validate["password"], $user->password))
        {
            if($user->verify)
            {
            $data = [
                "id"=>$user->id,
                "email"=>$validate["email"],
                "password"=>$validate["password"]
            ];

            $jwt = (new JwtController)->jwt_encode($data);

            User::where("email",$user->email)->update(["remember_token"=>$jwt]);
            //$user->remember_token=$jwt;
            //User::where("email",$user->email)->update(["remember_token"=>$jwt]);

            $response = [
                "status"=>"success",
                "token"=> $jwt
            ];
            return response()->success($response,200);

        }
        else{
            $response = ["status"=>"failed","message"=>"Your mail is not verified"];
            return response()->error($response,403);
        }
    }

        else
            {
                $response = ["status"=>"failed","message"=>"Either email or Password was wrong"];
                return response()->error($response,400);
            }
        }
        catch(Exception $e)
        {
            return response()->error($e->getMessage(),400);
        }
            }

            public function verify($email)
            {
                if(User::where("email",$email)->value('verify') == 1)
                {
                    $m = ["You have already verified your account"];
                    return response()->error($m,404);
                }
                else
                {
                    $update=User::where("email",$email)->update(["verify"=>1]);
                    if($update){
                        return response()->success("Account verified",200);
                    }else{
                        return response()->error("Failed",400);
                    }
                }
            }
        }
