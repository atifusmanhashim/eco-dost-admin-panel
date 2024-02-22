<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Inventory;

use Illuminate\Http\Request;

use App\Exports\VehicleListExport;

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

use Excel;

use Carbon\Carbon;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Vehicle List Page
    public function index(Request $request)
    {

        try {

            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken(env('Ecodost Admin Access Token'))->accessToken;
                $request->session()->put('user_name',$user->name);
                $vehicle_lst_recs=[];
                $vehicle_query=vehicle::select('vehicle_id',
                                    'vehicle_name',
                                    'vehicle_serial_number',
                                    'vehicle_chasis_number',
                                    'vehicle_date_purchase',
                                    'vehicle_type',
                                    'vehicle_make',
                                    'vehicle_model',
                                    'vehicle_customer_name',
                                    'vehicle.created_at as created_at',
                                    'vehicle.updated_at as updated_at',
                                    'vehicle_isactive')
                                ->where('vehicle_isactive',true)
                                ->orderby('vehicle_id')->get();
                $v_i=0;
                foreach($vehicle_query as $itm)
                {
                    // $vehicle_lst_recs[]=$value;
                    if ($itm->vehicle_isactive==true)
                    {
                        $vehicle_status="Active";
                    } else {
                        $vehicle_status="In-Active";
                    }


                    $vehicle_lst_recs[$v_i]=[
                        "vehicle_id"=>$itm->vehicle_id,
                        "vehicle_name"=>$itm->vehicle_name,
                        "vehicle_serial_number"=>$itm->vehicle_serial_number,
                        "vehicle_chasis_number"=>$itm->vehicle_chasis_number,
                        "date_purchase"=>$itm->vehicle_date_purchase,
                        "vehicle_type"=>$itm->vehicle_type,
                        "vehicle_model"=>$itm->vehicle_model,
                        "vehicle_status"=>$vehicle_status,
                        "vehicle_customer"=>$itm->vehicle_customer_name,
                        "created_at"=>$itm->created_at,
                        "updated_at"=>$itm->updated_at,
                    ];
                    $v_i+=1;
                }

                return view('vehicle_lst',compact('vehicle_lst_recs'));
            } else {
                return Redirect('login');
            }
        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Root: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }

    }

    //Download Vehicle List Purchased by Customer
    public function download_template(Request $request)
    {
        //User Dashboard
        try {
            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);

                
                return Excel::download(new VehicleListExport, 'vehicles_list.csv');

            } else {
                return Redirect::to('/login');
            }

        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Root: ".$error_code." ".$e->getmessage().". Please check your process or report to Ecodost System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }
    }
    

    //Product Warranty Checker
    public function warranty_checker(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'part_number'=>'required|numeric|exists:inventory,part_number',
                'chasis_number'=>'required|numeric|exists:vehicle,vehicle_chasis_number'
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

                // Part Number Checking with Warranty Period Defined
                $req_part_number=$request->part_number;
                $req_chasis_number=$request->chasis_number;
                $part_number_warranty_days_chk=Inventory::select('part_name','part_number','warranty_period')
                                        ->where('part_number','=',$req_part_number)
                                        ->whereNotNull('warranty_period')
                                        ->where('warranty_period','>=',1)
                                        ->get();

                $chasis_number_pur_date_chk=Vehicle::select('vehicle_name','vehicle_type','vehicle_chasis_number','vehicle_date_purchase as purchase_date')
                                        ->where('vehicle_chasis_number','=',$req_chasis_number)
                                        ->whereNotNull('vehicle_date_purchase')
                                        ->get();

                if (count($part_number_warranty_days_chk)>0 && count($chasis_number_pur_date_chk)>0)
                {

                    // Inventory Data
                    $part_number_warranty_days_data=$part_number_warranty_days_chk[0];
                    $warranty_period=$part_number_warranty_days_data->warranty_period;
                    $part_name=$part_number_warranty_days_data->part_name;
                    $part_number=$part_number_warranty_days_data->part_number;


                    //Vehicle Chasis Data
                    $chasis_number_pur_date=$chasis_number_pur_date_chk[0];
                    $vehicle_name=$chasis_number_pur_date->vehicle_name;
                    $vehicle_type=$chasis_number_pur_date->vehicle_type;
                    $purchase_date=date('Y-m-d',strtotime($chasis_number_pur_date->purchase_date));

                    $warranty_start_date=Carbon::parse($purchase_date)->format('Y-m-d');
                    $warranty_end_date=Carbon::parse($purchase_date)->addDays($warranty_period)->format('Y-m-d');
                    
                    $check_today_date=Carbon::now()->format('Y-m-d');
                    
                    
                    if ($check_today_date>=$warranty_start_date && $check_today_date<=$warranty_end_date)
                    {
                        $warranty_check_msg="Warranty Exists";
                    } else {
                        $warranty_check_msg="Warranty Expired";
                    }

                    
                    // $warranty_check_msg="Warranty Exists";
                    $warranty_data=[
                        "warranty_start_date"=>$warranty_start_date,
                        "warranty_period_days"=>$warranty_period,
                        "warranty_end_date"=>$warranty_end_date,
                        "todays_date"=>Carbon::now()->format('Y-m-d')
                    ];

                } else {
                    $warranty_period=0;
                    $date_purchase=null;

                    $warranty_check_msg="Warranty Data for given part number ".$req_part_number." and vehicle chasis number ".$req_chasis_number." doesn't exist";
                    $warranty_data=[];
                }

                
                $status_code=200;
                $data=[
                    "msg"=>$warranty_check_msg,   
                    "request"=>$request->all(),
                    "status"=>$status_code,
                    "warranty_data"=>$warranty_data
                ];
                return response() -> json($data, $status_code);
            }
        } catch (Exception $e) {
            $error_message="Upload Inventory API Crash. Error: ".$e->getmessage();
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

    //Product Warranty Checker
    public function vehicle_warranty_register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'date_purchase'=>'date|date_format:Y-m-d',
                'chasis_number'=>'required|string'
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

                $valid_date=validate_date($request->date_purchase);
                if ($valid_date==true)
                {
                    $date_purchase=convertYmdToMdy($request->date_purchase);
                } else {
                    $date_purchase=Carbon::now()->format('Y-m-d');
                }
                
                $req_chasis_number=$request->chasis_number;
                $vehicle_chasis_number_chk=Vehicle::where('vehicle_chasis_number','=',$req_chasis_number)
                                        ->get();

                if (count($vehicle_chasis_number_chk)>=1)
                {
                    $vehicle_chasis_number=$vehicle_chasis_number_chk[0];
                    
                    $vehicle_chasis_number->vehicle_date_purchase=$date_purchase;
                    $vehicle_chasis_number->save();
                    $status_code=400;
                    $data=[
                            "msg"=>"Vehicle Warranty Already Registered",   
                            "success"=>false,
                            "request"=>$request->all(),
                            "status"=>$status_code,
                            "data"=>[]
                    ];


                } else {

                    $vehicle_chasis_number=Vehicle::create([
                        'vehicle_date_purchase'=>$date_purchase,
                        'vehicle_name'=>'Nil',
                        'vehicle_chasis_number'=>$req_chasis_number
                    ]);
                    $status_code=200;
                    $data=[
                            "msg"=>"Vehicle Warranty Registered Succesfully",   
                            "success"=>true,
                            "request"=>$request->all(),
                            "status"=>$status_code,
                            "data"=>[]
                    ];
                }
                return response() -> json($data, $status_code);
            }
        } catch (Exception $e) {
            $error_message="Upload Inventory API Crash. Error: ".$e->getmessage();
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
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show(Vehicle $vehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function edit(Vehicle $vehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehicle $vehicle)
    {
        //
    }
}
