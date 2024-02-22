<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorType;
use App\Models\City;

use Illuminate\Http\Request;


use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
#Use for Validation
use Illuminate\Support\Facades\Validator;

#Use for Authentication
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Redirect;

use Exception;

class VendorController extends Controller
{

    //Vendor Create API
    public function create_vendor(Request $request)
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
                    'name'=>'required|string|min:2',
                    'company_name'=>'required|string|min:2',
                    'phone_no'=>'required|string|min:11|unique:vendor,phone_no',
                    'password'=>'string|min:8',
                    'vendor_type_id'=>'numeric|exists:vendor_type,vendor_type_id',
                    'city_id'=>'numeric|exists:city,city_id'
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

                    $vendor_password=Hash::make($request->password);
                    $vendor_phoneno=$request->phone_no;
                   
                    $vendor_phoneno = ltrim($vendor_phoneno, '0');

                    if(strpos($vendor_phoneno, "+92")!== false){
                        $vendor_phoneno=$vendor_phoneno;
                    } else {
                        $vendor_phoneno="+92".$vendor_phoneno;
                    }
                    

                    $vendor_obj=Vendor::create([
                        'name'=>$request->name,
                        'company_name'=>$request->company_name,
                        'address'=>$request->address,
                        'phone_no'=>$vendor_phoneno,
                        'password'=>$vendor_password,
                        'vendor_type_id'=>$request->vendor_type_id,
                        'vendor_isactive'=>true,
                        "city_id"=>$request->city_id,
                        'status'=>true,
                    ]);
                    $status_code=200;
                    $data=[
                        "response"=>[
                            "msg"=>"New Dealer Added Successfully!",
                            "success"=>true,
                            "status"=>$status_code,
                            "data"=> $vendor_obj
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

    //Making Vendor In-active
    public function delete_vendor(Request $request)
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
                    'vendor_id'=>'required|numeric|exists:vendor,id'

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

                    $vendor_id = $request->vendor_id;

                    $myComplaint = Vendor::where('id','=',$vendor_id)->delete();

                    if($myComplaint)
                    {
                        $status_code=200;
                        $data=[
                            "response"=>[
                                "msg"=>"Deleted Dealer Successfully!",
                                "success"=>true,
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
                                "msg"=>"Dealer Not Found!",
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

    //Update Vendor Data
    public function update_vendor(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                    'vendor_id'=>'required|numeric|exists:vendor,id',
                    'name'=>'required|string|min:2',
                    'company_name'=>'required|string|min:2',
                    'city'=>'numeric|exists:city,city_id',
                    'phone_no'=>'required|string|min:11',
                    'vendor_type_id'=>'numeric|exists:vendor_type,vendor_type_id'
            ]);

            if ($validator->fails()) {
                    $status_code=400;
                    $data=[
                        "msg"=>implode(",",$validator->messages()->all()),
                        "status"=>$status_code
                    ];
                    return response() -> json($data, $status_code);
            } else {
                $vendor_id = $request->vendor_id;
                $vendor_phoneno=$request->phone_no;
                $vendor_phoneno = ltrim($vendor_phoneno, '0');

                if(strpos($vendor_phoneno, "+92")!== false){
                    $vendor_phoneno=$vendor_phoneno;
                } else {
                    $vendor_phoneno="+92".$vendor_phoneno;
                }
                    
                $vendor_phoneno_chk=Vendor::where('phone_no',$vendor_phoneno)->whereNotIn('id',[$vendor_id])->get();
                if (count($vendor_phoneno_chk)>0)
                {
                    $status_code=400;
                    $data=[
                        "msg"=>"Phone No. Already Exists",
                        "status"=>$status_code,
                    ];
                    return response() -> json($data, $status_code);
                } else {
                    $vendorEdit = Vendor::where('id','=',$vendor_id)
                        ->update([  'name'=>$request->name, 
                                'company_name'=>$request->company_name, 
                                'address'=>$request->address, 
                                'phone_no'=>$vendor_phoneno]);

                }


               
                  
                $vendor_obj=Vendor::select('id','city_id','address','phone_no','company_name','name','vendor_type_id')
                                        ->where('id','=',$vendor_id)
                                        ->first();
                if (!empty($vendor_obj->city_id))
                {
                    $city=City::select('city_id','city_name')->where('city_id','=',$vendor_obj->city_id)->first();
                    $vendor_obj->city_name=$city->city_name;
                } else {
                    $vendor_obj->city_id="";
                    $vendor_obj->city_name="";
                }

                if (!empty($vendor_obj->vendor_type_id))
                {
                    $vendor_type=VendorType::select('vendor_type_id','vendor_type_name')->where('vendor_type_isactive','=',true)->where('vendor_type_id','=',$vendor_obj->vendor_type_id)->first();
                    $vendor_obj->vendor_type_name=$vendor_type->vendor_type_name;
                } else {
                    $vendor_obj->vendor_type_id="";
                    $vendor_obj->vendor_type_name="";
                }
                $status_code=200;
                $data=[
                            "msg"=>"Edited Dealer Successfully!",
                            "success"=>true,
                            "status"=>$status_code,
                            "vendor"=>$vendor_obj,

                        
                ];
                return response() -> json($data, $status_code);
            }



           
        } catch (Exception $e) {
            $error_message="Update Vendor API Crash. Error: ".$e->getmessage();
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

    //Update Vendor Data
    public function update_vendor_admin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                    'vendor_id'=>'required|numeric|exists:vendor,id',
                    'name'=>'required|string|min:2',
                    'company_name'=>'required|string|min:2',
                    'city'=>'numeric|exists:city,city_id',
                    'phone_no'=>'required|string|min:11',
                    'vendor_type_id'=>'numeric|exists:vendor_type,vendor_type_id'
            ]);

            if ($validator->fails()) {
                    $status_code=400;
                    $data=[
                        "msg"=>implode(",",$validator->messages()->all()),
                        "status"=>$status_code
                    ];
                    return response() -> json($data, $status_code);
            } else {
                $vendor_id = $request->vendor_id;
                $vendor_phoneno=$request->phone_no;
                $vendor_phoneno = ltrim($vendor_phoneno, '0');

                if(strpos($vendor_phoneno, "+92")!== false){
                    $vendor_phoneno=$vendor_phoneno;
                } else {
                    $vendor_phoneno="+92".$vendor_phoneno;
                }
                    
                $vendor_phoneno_chk=Vendor::where('phone_no',$vendor_phoneno)->whereNotIn('id',[$vendor_id])->get();
                if (count($vendor_phoneno_chk)>0)
                {
                    $status_code=400;
                    $data=[
                       "response"=>[
                            "msg"=>"Phone No. Already Exists",
                            "status"=>$status_code,
                       ] 
                    ];
                    return response() -> json($data, $status_code);
                } else {
                
                    if(!empty($request->password))
                    {
                            $vendor_password=bcrypt($request->password);
    
                            $vendorEdit = Vendor::where('id','=',$vendor_id)
                                                ->update(['vendor_type_id'=>$request->vendor_type_id,
                                                        'name'=>$request->name, 
                                                        'city_id'=>$request->city,
                                                        'company_name'=>$request->company_name, 
                                                        'address'=>$request->address, 
                                                        'phone_no'=>$vendor_phoneno, 
                                                        'password'=>$vendor_password]);
                    } else {
                            $vendorEdit = Vendor::where('id','=',$vendor_id)
                                        ->update(['vendor_type_id'=>$request->vendor_type_id,
                                                    'name'=>$request->name, 
                                                    'city_id'=>$request->city,
                                                    'company_name'=>$request->company_name, 
                                                    'address'=>$request->address, 
                                                    'phone_no'=>$vendor_phoneno]);
                    }
                    $vendor_obj=Vendor::select('id','city_id','address','phone_no','company_name','name','vendor_type_id')
                                    ->where('id','=',$vendor_id)
                                    ->first();
                    if (!empty($vendor_obj->city_id))
                    {
                        $city=City::select('city_id','city_name')->where('city_id','=',$vendor_obj->city_id)->first();
                        $vendor_obj->city_name=$city->city_name;
                    } else {
                        $vendor_obj->city_id="";
                        $vendor_obj->city_name="";
                    }
    
                    if (!empty($vendor_obj->vendor_type_id))
                    {
                        $vendor_type=VendorType::select('vendor_type_id','vendor_type_name')->where('vendor_type_isactive','=',true)->where('vendor_type_id','=',$vendor_obj->vendor_type_id)->first();
                        $vendor_obj->vendor_type_name=$vendor_type->vendor_type_name;
                    } else {
                        $vendor_obj->vendor_type_id="";
                        $vendor_obj->vendor_type_name="";
                    }



                    $status_code=200;
                    $data=[
                            "response"=>[
                                "msg"=>"Edited Dealer Successfully!",
                                "success"=>true,
                                "status"=>$status_code,
                                "vendor"=>$vendor_obj,
                            ]
                    ];
                    return response() -> json($data, $status_code);
                }

                  

                
            }



           
        } catch (Exception $e) {
            $error_message="Update Vendor API Crash. Error: ".$e->getmessage();
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

    //Vendor List 
    public function index(Request $request)
    {

        try {

            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('Ecodost Admin Access Token')->accessToken;
                $request->session()->put('user_name',$user->name);
                $vendor_lst_recs=[];
                $vendor_query=Vendor::select('id','name','company_name',
                                    'address',
                                    'phone_no','vendor_type.vendor_type_id',
                                    'vendor_type.vendor_type_name',
                                    'vendor.city_id as city_id','city.city_name as city_name',
                                    'vendor_isactive')
                                ->leftjoin('vendor_type','vendor_type.vendor_type_id','=','vendor.vendor_type_id')
                                ->leftjoin('city','city.city_id','=','vendor.city_id')
                                ->where('vendor_isactive',true)
                                ->orderby('id')->get();
                $v_i=0;
                foreach($vendor_query as $itm)
                {
                    // $vendor_lst_recs[]=$value;
                    if ($itm->vendor_isactive==true)
                    {
                        $vendor_status="Active";
                    } else {
                        $vendor_status="In-Active";
                    }


                    $vendor_lst_recs[$v_i]=[
                        "vendor_id"=>$itm->id,
                        "name"=>$itm->name,
                        "company_name"=>$itm->company_name,
                        "address"=>$itm->address,
                        "city_id"=>$itm->city_id,
                        "city_name"=>$itm->city_name,
                        "phone_no"=>$itm->phone_no,
                        "vendor_status"=>$vendor_status,
                        "vendor_type_id"=>$itm->vendor_type_id,
                        "vendor_type_name"=>$itm->vendor_type_name,
                    ];
                    $v_i+=1;
                }

                return view('vendor',compact('vendor_lst_recs'));
            } else {
                return Redirect('login');
            }
        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Root: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }

    }

    //Opening Add Dealer Form
    public function addvendor(Request $request)
    {

        //User Dashboard
        try {

            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('Ecodost Admin Access Token')->accessToken;
                $request->session()->put('user_name',$user->name);

                $city_lst=City::orderBy('city_id')->get();

                $vendor_type=VendorType::select('vendor_type_id','vendor_type_name')->where('vendor_type_isactive','=',true)->get();

                return view('addvendor',compact('vendor_type','city_lst'));
            } else {
                return Redirect('login');
            }
        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Root: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }

    }

    //Update Dealer
    public function editvendor($id)
    {

        //User Dashboard
        try {

            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('Ecodost Admin Access Token')->accessToken;

                $sel_vendor_id=$id;
                $vendor=Vendor::where('id','=',$sel_vendor_id)->first();

                $city_lst=City::orderBy('city_id')->get();
                $vendor_type=VendorType::select('vendor_type_id','vendor_type_name')->where('vendor_type_isactive','=',true)->get();

                return view('editvendor',compact('sel_vendor_id','vendor','vendor_type','city_lst'));
            } else {
                return Redirect('login');
            }
        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Root: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }

    }

    //Login Dealer
    public function login_vendor(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone_no'=>'required|string|min:11',
                'password'=>'required|string|min:8',
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
                $vendor_password=bcrypt($request->password);
                $vendor_phoneno=$request->phone_no;
                $vendor_password_hash=Hash::make($request->password);
                $vendor_phoneno = ltrim($vendor_phoneno, '0');
                $vendor_phoneno="+92".$vendor_phoneno;
                $vendor_chk=Vendor::where('phone_no','=',$vendor_phoneno)->get();
          
                if (count($vendor_chk)>=1)
                {

                    $vendor_obj=$vendor_chk[0];
                    if(Hash::check($request->password,$vendor_obj->password)) {
                        $vendor_pass="Match";
                        if (!empty($vendor_obj->city_id))
                        {
                            $city=City::select('city_id','city_name')->where('city_id','=',$vendor_obj->city_id)->first();
                            $vendor_obj->city_name=$city->city_name;
                        } else {
                            $vendor_obj->city_id="";
                            $vendor_obj->city_name="";
                        }

                        if (!empty($vendor_obj->vendor_type_id))
                        {
                            $vendor_type=VendorType::select('vendor_type_id','vendor_type_name')->where('vendor_type_isactive','=',true)->where('vendor_type_id','=',$vendor_obj->vendor_type_id)->first();
                            $vendor_obj->vendor_type_name=$vendor_type->vendor_type_name;
                        } else {
                            $vendor_obj->vendor_type_id="";
                            $vendor_obj->vendor_type_name="";
                        }
                       
                        $vendor_details=[
                            "id"=>$vendor_obj->id,
                            "name"=>$vendor_obj->name,
                            "company_name"=>$vendor_obj->name,
                            "address"=>$vendor_obj->address,
                            "phone_no"=>$vendor_obj->phone_no,
                            "vendor_type_id"=>$vendor_obj->vendor_type_id,
                            "vendor_type_name"=>$vendor_obj->vendor_type_name,
                            "city_id"=>$vendor_obj->city_id,
                            "city_name"=>$vendor_obj->city_name
                        ];
                       
                        $status_code=200;
                        $data=[

                                "msg"=>"dealer login success",
                                "success"=>true,
                                "status"=>$status_code,
                                "vendor"=>$vendor_details,
                        ];
                   
                    } else {
                        $status_code=400;
                        $data=[

                                "msg"=>"Invalid Login Credentials",
                                "success"=>false,
                                "vendor"=>[],
                                "status"=>$status_code,
                            
                        ];
                       

                    }
                    
                    

                    
                    
                } else {
                    $status_code=400;
                    $data=[
                            "msg"=>"Invalid Login Credentials",
                            "success"=>false,
                            "vendor"=>[],
                            "status"=>$status_code,
                    ];
                }
                return response()->json($data,$status_code);
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

    //List of Dealers with respect to Cities
    public function dealers_list(Request $request)
    {
        try {
            $dealer_data=[];

            $dealers_types=[];
            $dt_i=0;
            $dealers_type_query=VendorType::where('vendor_type_isactive','=',true)->get();
            foreach($dealers_type_query as $dealer)
            {
                $dealers_types[$dt_i]=[
                    "vendor_type_id"=>$dealer->vendor_type_id,
                    "vendor_type_name"=>$dealer->vendor_type_name
                ];
                $dt_i+=1;
            }

            $city_lst=City::orderBy('city_id')->get();

            $d_i=0;
            foreach($city_lst as $city)
            {

                $dealers_lst=Vendor::select('id',
                                            'name',
                                            'company_name',
                                            'address',
                                            'phone_no',
                                            'vendor_type.vendor_type_id',
                                            'vendor_type.vendor_type_name',
                                            'city.city_id',
                                            'city.city_name')
                                        ->leftjoin('vendor_type','vendor_type.vendor_type_id','=','vendor.vendor_type_id')
                                        ->leftjoin('city','city.city_id','=','vendor.city_id')
                                        ->where('vendor_isactive',true)
                                        ->where('vendor.city_id','=',$city->city_id)
                                        ->orderby('id')
                                        ->get();
                $res=[];
                foreach ($dealers_lst as $key=>$value)
                {
                    $res[]=$value;
                }
                
                $dealer_data[$d_i]=[
                    "city"=>$city->city_name,
                    "dealers"=>$res
                ];
                $d_i+=1;
            }
            $status_code=200;
            $data=[
                "msg"=>"success",
                "status"=>$status_code,
                "dealers_type"=>$dealers_types,
                "cities"=>$city_lst->toArray(),
                "data"=>$dealer_data
                
            ];
            return response()->json($data,$status_code);
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

    //List of Dealers with respect to Dealer type
    public function dealers_list_type(Request $request)
    {
        try {
            $dealer_data=[];

            $dealers_types=[];
            $dt_i=0;
            $d_i=0;
            $dealers_type_query=VendorType::where('vendor_type_isactive','=',true)->get();
            foreach($dealers_type_query as $dealer)
            {
                $dealers_types[$dt_i]=[
                    "vendor_type_id"=>$dealer->vendor_type_id,
                    "vendor_type_name"=>$dealer->vendor_type_name
                ];
                $dealers_lst=Vendor::select('id',
                                            'name',
                                            'company_name',
                                            'address',
                                            'phone_no',
                                            'vendor_type.vendor_type_id',
                                            'vendor_type.vendor_type_name',
                                            'city.city_id',
                                            'city.city_name')
                                        ->leftjoin('vendor_type','vendor_type.vendor_type_id','=','vendor.vendor_type_id')
                                        ->leftjoin('city','city.city_id','=','vendor.city_id')
                                        ->where('vendor_isactive',true)
                                        ->where('vendor.vendor_type_id','=',$dealer->vendor_type_id)
                                        ->orderby('id')
                                        ->get();
                if (count($dealers_lst)>=1)
                {
                    $res=[];
                    foreach ($dealers_lst as $key=>$value)
                    {
                        $res[]=$value;
                    }
                    
                    $dealer_data[$d_i]=[
                        "Type"=>$dealer->vendor_type_name,
                        "dealers"=>$res
                    ];
                    $d_i+=1;
                    
                }
                $dt_i+=1;
            }

            $city_lst=City::orderBy('city_id')->get();

           
            $status_code=200;
            $data=[
                "msg"=>"success",
                "status"=>$status_code,
                "dealers_type"=>$dealers_types,
                "cities"=>$city_lst->toArray(),
                "data"=>$dealer_data
                
            ];
            return response()->json($data,$status_code);
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

}
