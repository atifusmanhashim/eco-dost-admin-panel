<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FAQ;

use Illuminate\Support\Str;

#Use for Validation
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;

use Exception;

use Redirect;

class FAQController extends Controller
{

    public function index(Request $request)
    {

        try {

            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('Ecodost Admin Access Token')->accessToken;
                $request->session()->put('user_name',$user->name);
                $request->session()->put('user_id',$user->id);
                $faq_lst_recs=[];
                // $request_status = $request->request_status;
                // $status_need = [];



                $faq_obj=FAQ::where('faq_isactive',true)->orderby('id','desc')->get();


                $f_i=0;
                foreach($faq_obj as $itm)
                {
                    if ($itm->faq_isactive==true)
                    {
                        $faq_status="Active";
                    } else {
                        $faq_status="In-Active";
                    }
                    $faq_lst_recs[$f_i]=[
                        "faq_id"=>$itm->id,
                        "faq_question"=>$itm->question,
                        "faq_answer"=>$itm->answer,
                        "faq_status"=>$faq_status,
                        "created_at"=>$itm->created_at->format('d-m-y')
                    ];

                    $f_i+=1;
                }
                return view('faqs',compact('faq_lst_recs'));
            } else {
                return Redirect::to('/login');
            }
        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Root: ".$error_code." ".$e->getmessage().". Please check your process or report to Ecodost System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }

    }

    public function addfaq(Request $request)
    {

        //User Dashboard
        try {

            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('Ecodost Admin Access Token')->accessToken;
                $request->session()->put('user_name',$user->name);

                // $vendor_type=VendorType::select('vendor_type_id','vendor_type_name')->where('vendor_type_isactive','=',true)->get();

                return view('addfaq');
            } else {
                return Redirect('login');
            }
        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Root: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }

    }

    public function faq_insert(Request $request)
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
                    'question'=>'required|string',
                    'answer'=>'required|string'
                ]);

                if ($validator->fails()) {
                    $status_code=400;
                    $data=[
                        "response"=>[
                            "msg"=>implode(",",$validator->messages()->all()),
                            "status"=>$status_code
                        ]
                    ];
                    return response() -> json($data, $status_code);
                } else {

                    $faq_object=FAQ::create([
                        'question'=>$request->question,
                        'answer'=>$request->answer
                    ]);
                    $status_code=200;
                    $data=[
                        "response"=>[
                            "msg"=>"Added new FAQ successfully!",
                            "success"=>true,
                            "status"=>$status_code,
                            "data"=> $faq_object
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
            $error_message="Add Complaint API Crash. Error: ".$e->getmessage();
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

    public function delete_faq(Request $request)
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
                    'id'=>'required|numeric|exists:faq,id'
                ]);

                if ($validator->fails()) {
                    $status_code=400;
                    $data=[
                        "response"=>[
                            "msg"=>implode(",",$validator->messages()->all()),
                            "status"=>$status_code
                        ]
                    ];
                    return response() -> json($data, $status_code);
                } else {

                    $faq_object=FAQ::where('id','=',$request->id)->update(['faq_isactive'=>false]);
                    $status_code=200;
                    $data=[
                        "response"=>[
                            "msg"=>"Deleted FAQ Successfully!",
                            "success"=>true,
                            "status"=>$status_code,
                            "data"=> $faq_object
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
            $error_message="Add Complaint API Crash. Error: ".$e->getmessage();
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

    public function get_faqs(Request $request)
    {
        try {
            $faq_obj=FAQ::where('faq_isactive','=',true)->get();


            foreach ($faq_obj  as $key => $value) {
                $res[] = $value;
            }




            $status_code=200;
            $data=[
                "response"=>[
                    "msg"=>"success",
                    "status"=>$status_code,
                    "data"=>$res
                ]
            ];
            return response() -> json($data, $status_code);




           
               
        } catch (Exception $e) {
            $error_message="List of Priorities API Crash. Error: ".$e->getmessage();
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

    public function get_faq_by_id(Request $request, $id)
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
                $faq_obj=FAQ::where('id', '=', $id)->first();


                // foreach ($faq_obj  as $key => $value) {
                //     $res[] = $value;
                // }




                $status_code=200;
                $data=[
                    "response"=>[
                        "msg"=>"success",
                        "success"=>true,
                        "status"=>$status_code,
                        "data"=>$faq_obj
                    ]
                ];
                return response() -> json($data, $status_code);




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
            $error_message="List of Priorities API Crash. Error: ".$e->getmessage();
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


    public function update_faq(Request $request)
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
                    'id'=>'required|numeric|exists:faq,id',
                    'question'=>'required|string',
                    'answer'=>'required|string'
                ]);

                if ($validator->fails()) {
                    $status_code=400;
                    $data=[
                        "response"=>[
                            "msg"=>implode(",",$validator->messages()->all()),
                            "status"=>$status_code
                        ]
                    ];
                    return response() -> json($data, $status_code);
                } else {

                    $faq_object=FAQ::where('id','=',$request->id)->update(['question'=>$request->question, 'answer'=>$request->answer]);
                    $status_code=200;
                    $data=[
                        "response"=>[
                            "msg"=>"Updated FAQ Successfully!",
                            "status"=>$status_code,
                            "data"=> $faq_object
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
            $error_message="Add Complaint API Crash. Error: ".$e->getmessage();
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


    public function editfaq($id)
    {

        //User Dashboard
        try {

            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('Ecodost Admin Access Token')->accessToken;

                $sel_faq_id=$id;

                $faq=FAQ::where('id','=',$sel_faq_id)->first();

                // $vendor_type=VendorType::select('vendor_type_id','vendor_type_name')->where('vendor_type_isactive','=',true)->get();

                return view('editfaq',compact('sel_faq_id','faq'));
            } else {
                return Redirect('login');
            }
        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Root: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }

    }
}
