<?php

namespace App\Http\Controllers;

use App\Models\Priority;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Str;

#Use for Authentication
use Illuminate\Support\Facades\Auth;

#Use for Validation
use Illuminate\Support\Facades\Validator;



class PriorityController extends Controller
{
    //Add Priotities
    public function add_priority(Request $request)
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
                    'name'=>'required|string|unique:priority,name',
                    'priority_lvl'=>'required|numeric|min:1|max:10',
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

                    $priority_obj=Priority::create([
                        'name'=>$request->name,
                        'priority_lvl'=>$request->priority_lvl,
                        'priority_isactive'=>true
                    ]);
                    $status_code=200;
                    $data=[
                        "response"=>[
                            "msg"=>"success",
                            "status"=>$status_code,
                            "data"=>$priority_obj
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
            $error_message="Add Priority API Crash. Error: ".$e->getmessage();
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

    public function priority_list(Request $request)
    {
        try {
            $priority_objs=Priority::where('priority_isactive','=',true)->orderby('priority_lvl')->get();


            foreach ($priority_objs  as $key => $value) {
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Models\Priority  $priority
     * @return \Illuminate\Http\Response
     */
    public function show(Priority $priority)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Priority  $priority
     * @return \Illuminate\Http\Response
     */
    public function edit(Priority $priority)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Priority  $priority
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Priority $priority)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Priority  $priority
     * @return \Illuminate\Http\Response
     */
    public function destroy(Priority $priority)
    {
        //
    }
}
