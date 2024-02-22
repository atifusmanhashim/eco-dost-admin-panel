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


class RegisterController extends Controller
{
    public function index(Request $request)
    {
        return view('auth.register');
    }
    public function otpscreen(Request $request)
    {
        return view('auth.otpScreen');
    }
    public function adduser(Request $request)
    {
        return view('adduser');
    }

    public function verify_email (Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users,email'
            ]);
            if ($validator->fails()) {
                $status_code=400;
                $data=[
                    "msg"=>implode(",",$validator->messages()->all()),
                    "status"=>$status_code
                    
                ];
                return response() -> json($data, $status_code);
            } else {
                $status_code=200;
                $data=[
                    "response"=>[
                        "msg"=>"Email Available",
                        // "msg"=>"error",
                        "status"=>$status_code
                    ]
                ];
                return response() -> json($data, $status_code);
            }
        } catch (Exception $e) {
            $error_message="Verify Email API Crash";
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

    //User Registration
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users,email',
                    'password' => 'required|string|min:6',
                    'contact_no'=>'string|unique:users,contact_no',
                    'role_id' => 'required|numeric|exists:role,id',
                    'location_id' => 'required|numeric|exists:location,id',
            ]);

            if ($validator->fails()) {
                $status_code=400;
                $data=[
                    "response"=>[
                        "msg"=>implode(",",$validator->messages()->all()),
                        // "msg"=>"error",
                        "status"=>$status_code
                    ]
                ];
                return response() -> json($data, $status_code);
            } else {
                $user_email=$request->email;
                $user_name=$request->name;
                $user_password=bcrypt($request->password);
                $user_salt=bcrypt(str::random(12));

                $location_id=$request->location_id;
                $role_id=$request->role_id;

                if (!empty($request->contact_no))
                {
                    $user_contact_no = ltrim($request->contact_no, '0');
    
                    if(strpos($user_contact_no, "+92")!== false){
                        $user_contact_no=$user_contact_no;
                    } else {
                        $user_contact_no="+92".$user_contact_no;
                    }
                } else {
                    $user_contact_no="";
                }

                $user = User::create([
                    'name' => $user_name,
                    'email' => $user_email,
                    'password' => $user_password,
                    'salt'=>$user_salt,
                    'hash'=>$user_password,
                    'user_active'=>true,
                    'is_auth'=>true,
                    'location_id'=>$location_id,
                    'role_id'=>$role_id,
                    'contact_no'=>$user_contact_no
                ]);
                $token = $user->createToken(env('TOKEN_NAME'))->accessToken;
                $response = ['token' => $token];

                

                $status_code=200;
                $data=[
                            "response"=>[
                                "msg"=>"User is registered Successfully",
                                "success"=>true,
                                "status"=>$status_code,
                                "data"=>[
                                    "id"=>$user->id,
                                    "name"=>$user->name,
                                    "provider_id"=>0,
                                    "email"=>$user->email,
                                    'location_id'=>$user->location_id,
                                    'role_id'=>$user->role_id,
                                    'contact_no'=>$user->contact_no,
                                    "is_auth"=>true,
                                    "token"=>$token
                                ]
                            ]
                ];
                return response()->json($data, $status_code);
            }


           








        } catch (Exception $e) {
            $error_message="User Register API Crash";
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

    public function delete_user(Request $request)
    {
        try {
            $user_token=$request->header('Authorization', ''); // Taking User Token from Authorization header
            if (Str::startsWith($user_token, 'Bearer ')) {   // If authorization type is Bearer
                $user_token=Str::substr($user_token, 7);
            } elseif (Str::startsWith($user_token, 'Basic ')) {  // If authorization type is Basic
                $user_token=Str::substr($user_token, 6);
            } else {
                $user_token=null;
            }
            if (Auth::guard('api')->check())
            {
                $validator = Validator::make($request->all(), [
                    'user_id'=>'required|numeric|exists:users,id'

                ]);

                if ($validator->fails()) {
                    $status_code=400;
                    $data=[
                        "response"=>[
                            "msg"=>implode(",",$validator->messages()->all()),
                            // "msg"=>"error",
                            "status"=>$status_code
                        ]
                    ];
                    return response() -> json($data, $status_code);
                } else {

                    $user_id = $request->user_id;
                    
                    $userDelete = User::where('id','=',$user_id)->delete();

                    if($userDelete)
                    {
                        $status_code=200;
                        $data=[
                            "response"=>[
                                "msg"=>"Deleted User Successfully!",
                                "status"=>$status_code
                            ]
                        ];
                        return response() -> json($data, $status_code);
                    }
                    else
                    {
                        $status_code=404;
                        $data=[
                            "response"=>[
                                "msg"=>"User Not Found!",
                                "status"=>$status_code
                            ]
                        ];
                        return response() -> json($data, $status_code);
                    }

                }



            } else {
                $status_code=401;
                $data=[
                    "response"=>[
                        "msg"=>"You are not authorised",
                        "success"=>false,
                        "status"=>401
                    ]
                ];
                return response()->json($data, $status_code);

            }
        } catch (Exception $e) {
            $error_message="Create Vendor API Crash. Error: ".$e->getmessage();
            //$error_message=$e->getmessage();
            $status_code=400;
            $data=[
                "response"=>[
                    "msg"=>$error_message,
                    "status"=>$status_code,
                ]
            ];
            return response()->json($data,$status_code);

        }
    }

    public function update_user_details(Request $request)
    {
        try {
            $user_token=$request->header('Authorization', ''); // Taking User Token from Authorization header
            if (Str::startsWith($user_token, 'Bearer ')) {   // If authorization type is Bearer
                $user_token=Str::substr($user_token, 7);
            } elseif (Str::startsWith($user_token, 'Basic ')) {  // If authorization type is Basic
                $user_token=Str::substr($user_token, 6);
            } else {
                $user_token=null;
            }
            if (Auth::guard('api')->check())
            {
                $validator = Validator::make($request->all(), [
                    'user_id'=>'required|numeric|exists:users,id',
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255',
                    'role_id' => 'required|numeric|exists:role,id',
                    'location_id' => 'required|numeric|exists:location,id'
                ]);


                if ($validator->fails()) {
                    $status_code=400;
                    $data=[
                        "response"=>[
                            "msg"=>implode(",",$validator->messages()->all()),
                            // "msg"=>"error",
                            "status"=>$status_code
                        ]
                    ];
                    return response() -> json($data, $status_code);
                } else {
                    $user_id = $request->user_id;
                    if (!empty($request->contact_no))
                    {
                        $user_contact_no = ltrim($request->contact_no, '0');

                        if(strpos($user_contact_no, "+92")!== false){
                            $user_contact_no=$user_contact_no;
                        } else {
                            $user_contact_no="+92".$user_contact_no;
                        }

                        $user_phoneno_chk=User::where('contact_no',$user_contact_no)->whereNotIn('id',[$user_id])->get();
                        if (count($user_phoneno_chk)>0)
                        {
                            $status_code=400;
                            $data=[
                                "response"=>[
                                    "msg"=>"Phone No. Already Exists",
                                    "status"=>$status_code,
                                ]
                            ];
                            return response() -> json($data, $status_code);
                        }
                    } else {
                        $user_contact_no="";
                    }
                    
                    // $userDelete = User::where('id','=',$user_id)->update(['user_active'=>true]);

                    if(empty($request->password))
                    {
                        $userEdit = User::where('id','=',$user_id)->update(['name'=>$request->name, 'email'=>$request->email, 'contact_no'=>$user_contact_no, 'role_id'=>$request->role_id, 'location_id'=>$request->location_id]);
                    }
                    else
                    {
                        $user_password=bcrypt($request->password);

                        $userEdit = User::where('id','=',$user_id)->update(['name'=>$request->name, 'email'=>$request->email, 'contact_no'=>$user_contact_no, 'role_id'=>$request->role_id, 'location_id'=>$request->location_id, 'password'=>$user_password]);
                    }
                    
                    $status_code=200;
                    $data=[
                        "response"=>[
                            "msg"=>"Edited User Successfully!",
                            "status"=>$status_code,
                        ]
                    ];
                    return response() -> json($data, $status_code);
                }



            } else {
                $status_code=401;
                $data=[
                    "response"=>[
                        "msg"=>"You are not authorised",
                        "success"=>false,
                        "status"=>401
                    ]
                ];
                return response()->json($data, $status_code);

            }
        } catch (Exception $e) {
            $error_message="Update User API Crash. Error: ".$e->getmessage();
            //$error_message=$e->getmessage();
            $status_code=400;
            $data=[
                "response"=>[
                    "msg"=>$error_message,
                    "status"=>$status_code,
                ]
            ];
            return response()->json($data,$status_code);

        }
    }
}
