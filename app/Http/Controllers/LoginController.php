<?php

namespace App\Http\Controllers;

//User Model
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserLocation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
#Use for Authentication
use Illuminate\Support\Facades\Auth;

use Session;

#Using for Http Client
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

#Passport Client
use Laravel\Passport\Client as OClient;

//Use for Try Catch
use Exception;

use Redirect;

#Use for Validation
use Illuminate\Support\Facades\Validator;



class LoginController extends Controller
{


   //Loading Login Page
   public function index(Request $request)
    {
        try {
            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('SastaTareen Admin Access Token')->accessToken;
                $request->session()->put('user_name',$user->name);
                $request->session()->put('token',$access_token);

                return Redirect::to('/dashboard');
            } else {
                return view('auth.login');
            }
        } catch (Exception $e) {
            $error_code="A003";
            $error_msg="Error in User Login: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }

    }

    //User Login
    public function login(Request $request)
    {
        try {

            $login_validator=Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            if ($login_validator->fails()) {
                    $status_code=400;

                    $data=[
                        "response"=>[
                            "msg"=>"Error",
                            "success"=>false,
                            "status"=>$status_code,
                            "errors"=>implode(",",$validator->messages()->all()),
                        ]
                    ];
                    return response()->json($data, $status_code);


            } else {


                //Declaration of credentials
                $credentials = $request->validate([
                    'email' => 'required|string|email',
                    'password' => 'required|string',
                ]);

                if (auth()->attempt($credentials))
                {

                    //Creating User Object after successful login
                    $user = Auth::user();

                    Auth::login($user);


                    $token = $user->createToken(env('TOKEN_NAME'))->accessToken;



                    $status_code=200;
                    $data=[
                        "response"=>[
                            "msg"=>"User is login successfully",
                            "status"=>$status_code,
                            "data"=>[
                                "id"=>$user->id,
                                "name"=>$user->name,
                                "provider_id"=>0,
                                "email"=>$user->email,
                                "is_auth"=>true,
                                "redirect"=>url("dashboard"),
                                "token"=>$token,
                            ]
                        ]
                    ];

                    return response()->json($data, $status_code);
                } else {
                    $status_code=402;
                    $data=[
                        "response"=>[
                            "msg"=>"Invalid credentials",
                            "success"=>false,
                            "status"=>$status_code,
                            "data"=>[]
                        ]
                    ];
                    return response()->json($data, $status_code);

                }
            } // End of Validator




        } catch (Exception $e) {
                $error_message="User Login API Crash";
                $error_message=$e->getmessage();
                $status_code=400;
                $data=[
                    "response"=>[
                        "msg"=>"API Error # ".$error_message,
                        "status"=>$status_code,
                    ]
                ];
                return response()->json($data,$status_code);
        }


    }


    //Post Login after submission of Login
    public function postLogin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user=Auth::user();
                Auth::login($user);

                $access_token = $user->createToken(env('TOKEN_NAME'))->accessToken;
                $user_role_id = $user->role_id;

                $request->session()->put('user_name',$user->name);
                $request->session()->put('token',$access_token);
                $request->session()->put('role_id',$user_role_id);
                return redirect()->intended('dashboard')
                            ->withSuccess('You have Successfully loggedin');
            } else {
                return redirect("login")->withErrors(['msg' => 'Invalid Login Credential']);
            }
        } catch (Exception $e) {
            $error_code="A004";
            $error_msg="Error in User Post Login: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }
    }

    public function logout()
    {
        try {
            Session::flush();
            Auth::logout();
            return redirect::to('/login');
        } catch (Exception $e) {
            $error_code="A005";
            $error_msg="Error in Logout: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }


    }


}
