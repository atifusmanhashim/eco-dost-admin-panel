<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Vendor;
use App\Models\Priority;
use App\Models\User;
use App\Models\ComplaintAttachment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Str;

#Use for Validation
use Illuminate\Support\Facades\Validator;

#Use for Authentication
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\App;

use Illuminate\Support\Carbon as Carbon;

use Redirect;

use Exception;

use Mail;

use URL;

use App\Mail\NewComplaintEmail;
class ComplaintController extends Controller
{

    //Complaints Main Page
    public function index(Request $request)
    {

        //User Dashboard
        try {

            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('Ecodost Admin Access Token')->accessToken;
                $request->session()->put('user_name',$user->name);
                $request->session()->put('user_id',$user->id);
                $complaint_lst_recs=[];
                $request_status = $request->request_status;
                $status_need = [];

                if(!empty($request_status))
                {
                    if ($request_status == "Pending")
                    {
                        $request_status_desc="Pending";
                        $status_need = ["Pending"];
                        $warranty_chk=[1,0];
                    } elseif ($request_status == "InProcess") {
                        $request_status_desc="In Process";
                        $status_need = ["In Process"];
                        $warranty_chk=[1,0];
                    } elseif($request_status == "Resolved") {
                        $request_status_desc="Resolved";
                        $status_need = ["Resolved"];
                        $warranty_chk=[1,0];
                    } elseif($request_status == "Cancelled") {
                        $request_status_desc="Cancelled";
                        $status_need = ["Cancelled"];
                        $warranty_chk=[1,0];
                    } elseif($request_status == "warranty") {
                        $request_status_desc="Warranty Claims";
                        $status_need = ["Pending","In Process","Resolved","Cancelled"];
                        $warranty_chk=[1];
                        
                    } else {
                        $request_status_desc="All Complaints";
                        $status_need = ["Pending","In Process", "Resolved","Cancelled"];
                        $warranty_chk=[1,0];
                    }
                } else {
                    $request_status_desc="All Complaint(s)";
                    $status_need = ["Pending","In Process", "Resolved","Cancelled"];
                    $warranty_chk=[1,0];
                }

                $complaint_status_lst=complaint_status_lst();

                $complaint_query=Complaint::select('complaint.id as complaint_id',
                                    'complaint_no',
                                    'part_name',
                                    'part_number',
                                    'description',
                                    'make',
                                    'model',
                                    'chasis_number',
                                    'battery_number',
                                    'motor_number',
                                    'complaint_status',
                                    'complaint.priority_id as priority_id',
                                    'priority.name as priority_name',
                                    'complaint.vendor_id',
                                    'vendor.name as vendor_name',
                                    'customer_name',
                                    'complaint.warranty',
                                    'complaint.comment_by_admin',
                                    'complaint.complaint_isactive',
                                    'complaint.user_id',
                                    'complaint.created_at',
                                    'complaint.updated_at')
                                    ->leftjoin('priority','priority.id','=','complaint.priority_id')
                                    ->leftjoin('vendor','vendor.id','=','complaint.vendor_id')
                                    ->wherein('complaint_status', $status_need)
                                    ->wherein('warranty',$warranty_chk)
                                    ->where('complaint_isactive','=',true)
                                    ->orderby('priority_lvl')
                                    ->orderby('complaint.id','desc')
                                    ->get();
                $c_i=0;
                foreach($complaint_query as $itm)
                {
                    if ($itm->warranty==true)
                    {
                        $warranty_desc="Yes";
                    } else {
                        $warranty_desc="No";
                    }
                    $priority_obj=Priority::where('id','=',$itm->priority_id)->first();
                    $complaint_lst_recs[$c_i]=[
                        "complaint_id"=>$itm->complaint_id,
                        'complaint_no'=>$itm->complaint_no,
                        "part_name"=>$itm->part_name,
                        "part_number"=>$itm->part_number,
                        "description"=>$itm->description,
                        "make"=>$itm->make,
                        "model"=>$itm->model,
                        'chasis_number'=>$itm->chasis_number,
                        "battery_number"=>$itm->battery_number,
                        "motor_number"=>$itm->motor_number,
                        "complaint_status"=>$itm->complaint_status,
                        "priority_id"=>$itm->priority_id,
                        "priority_name"=>$priority_obj->name,
                        "vendor_id"=>$itm->vendor_id,
                        "vendor_name"=>$itm->vendor_name,
                        'customer_name'=>$itm->customer_name,
                        "comment_by_admin"=>$itm->comment_by_admin,
                        "complaint_isactive"=>$itm->complaint_isactive,
                        "warranty"=>$itm->warranty,
                        "warranty_desc"=>$warranty_desc,
                        "created_at"=>$itm->created_at->format('d-m-y'),
                        "user_id"=>$itm->user_id
                    ];

                    $c_i+=1;
                }
                return view('complaint',compact('complaint_lst_recs','request_status_desc','complaint_status_lst'));
            } else {
                return Redirect::to('/login');
            }
        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Complaints List Page: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }

    }


    // Pending for Approvals of Complaints
    public function pending_approval(Request $request)
    {

        //User Dashboard
        try {

            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('Ecodost Admin Access Token')->accessToken;
                $request->session()->put('user_name',$user->name);
                $request->session()->put('user_id',$user->id);
                $complaint_lst_recs=[];

                $status_need = [];

                $status_need = ["Pending"];
                $request_status=["Resolved"];


                $complaint_status_lst=complaint_status_lst();
                $complaint_query=Complaint::select('complaint.id as complaint_id','description',
                                    'complaint.priority_id as priority_id','priority.name as priority_name',
                                    'vendor.name as vendor_name',
                                    'part_name',
                                    'make',
                                    'model',
                                    'battery_number',
                                    'motor_number',
                                    'complaint_status',
                                    'warranty',
                                    'complaint.created_at',
                                    'complaint.updated_at')
                                    ->leftjoin('priority','priority.id','=','complaint.priority_id')
                                    ->leftjoin('vendor','vendor.id','=','complaint.vendor_id')
                                    ->wherein('complaint_status', $status_need)
                                    ->where('complaint_isactive','=',true)
                                    ->where('warranty','=',true)
                                    ->where('approval_chk','=',0)
                                    ->orderby('priority_lvl')
                                    ->orderby('complaint.id','desc')
                                    ->get();
                $c_i=0;
                foreach($complaint_query as $itm)
                {
                    if ($itm->warranty==true)
                    {
                        $warranty_desc="Yes";
                    } else {
                        $warranty_desc="No";
                    }
                    $priority_obj=Priority::where('id','=',$itm->priority_id)->first();
                    $complaint_lst_recs[$c_i]=[
                        "complaint_id"=>$itm->complaint_id,
                        "priority_id"=>$itm->priority_id,
                        "priority_name"=>$priority_obj->name,
                        "complaint_id"=>$itm->complaint_id,
                        "part_name"=>$itm->part_name,
                        "make"=>$itm->make,
                        "model"=>$itm->model,
                        "battery_number"=>$itm->battery_number,
                        "motor_number"=>$itm->motor_number,
                        "description"=>$itm->description,
                        "vendor_name"=>$itm->vendor_name,
                        "complaint_status"=>$itm->complaint_status,
                        "warranty"=>$itm->warranty,
                        "warranty_desc"=>$warranty_desc,
                        "created_at"=>$itm->created_at->format('d-m-y')
                    ];

                    $c_i+=1;
                }
                $total_approvals=count($complaint_lst_recs);
                $pending_approval_complaints=$complaint_lst_recs;
                return view('pending_approval_complaints',compact('pending_approval_complaints','request_status','total_approvals'));
            } else {
                return Redirect::to('/login');
            }
        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Pending for Approvals of Complaints: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }

    }

    //Add Complaints
    public function add_complaint(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'priority_id'=>'required|numeric|exists:priority,id',
                'vendor_id'=>'required|numeric|exists:vendor,id',
                'inventory_id'=>'numeric|exists:inventory,id',
                'part_name'=>'string',
                'description'=>'string',
                'make'=>'string',
                'model'=>'string',
                'customer_name'=>'string',
                'serial_number'=>'string',
                'part_number'=>'string',
                'chasis_number'=>'string',
                'battery_number'=>'string',
                'motor_number'=>'string',
                'complaint_img_file' => 'mimes:jpeg,png,jpg,gif'
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
                    $complaint_attachments=[];
                    $complaint_no="C-".Str::random(12);
                    $sel_part_number=$request->part_number;
                    $sel_chasis_number=$request->chasis_number;
                    $warranty_value=warranty_check($sel_part_number,$sel_chasis_number);
                    
                   
                    $complaint_obj=Complaint::create([
                        'complaint_no'=>$complaint_no,
                        'part_name'=>$request->part_name,
                        'description'=>$request->description,
                        'make'=>$request->make,
                        'model'=>$request->model,
                        'customer_name'=>$request->customer_name,
                        'part_number'=>$request->part_number,
                        'serial_number'=>$request->serial_number,
                        'chasis_number'=>$request->chasis_number,
                        'battery_number'=>$request->battery_number,
                        'motor_number'=>$request->motor_number,
                        'priority_id'=>$request->priority_id,
                        'vendor_id'=>$request->vendor_id,
                        'complaint_status'=>'Pending',
                        'complaint_active'=>true,
                        'warranty'=>$warranty_value
                    ]);

                    // Image File
                    $complaint_img_files = [];
                    if ($request->file('complaint_img_file'))
                    {
                        $base_folder = 'complaint_image_attachments'; // Your desired sub-folder name
                        
                        $subFolder=$base_folder . "/" .$complaint_no;

                        $image = $request->file('complaint_img_file'); 

                        $allowedfileExtension=['gif','jpg','png'];
    
                        $file_exist=0;

                        // Image File Name without Extension
                        $img_baseName=pathinfo($image->getClientOriginalName());

                        $img_extension=$image->getClientOriginalExtension();

                        // echo($img_extension);

                        // Generate a unique filename using the original file's extension
                        $filename = $img_baseName['filename'] . '.' . $image->getClientOriginalExtension();

                        // Store the file in the sub-folder on S3 with the original filename
                        $image_filePath = $subFolder .'/' . $filename;
                            
                        $image_save_path=Storage::disk('s3')->put($image_filePath, file_get_contents($image));
                            
                        // Optionally, you can get the public URL of the uploaded image
                        $img_url = Storage::disk('s3')->url($image_filePath);
                            
                            
                            
                        $complaint_attachments_save=ComplaintAttachment::create([
                            'url'=>$img_url,
                            'image_file_name'=>$filename,
                            'complaint_id'=>$complaint_obj->id,
                            'status'=>true
                        ]);

                        $complaint_img_files[$file_exist]=$complaint_attachments_save->url;

                        //Multiple File Attachments
                        // $files = $request->file('complaint_img_file'); 
                        // $files = [];
                        // foreach($request->file('complaint_img_file') as $image){
                        //     $files[] = $image;
                        // }
                       
                      
                        // $file_exist=0;
                        // foreach ($files as $image_file) 
                        // {

                        //     $extension = $image_file->getClientOriginalExtension();
                        //     $check = in_array($extension,$allowedfileExtension);
                        //     if($check) {
                        //         $image=$image_file;
                        //         $img_baseName=pathinfo($image->getClientOriginalName());
                        //         $img_extension=$image->getClientOriginalExtension();
                        //         $img_filename = $img_baseName['filename'] . '.' . $image->getClientOriginalExtension();
                        //         $image_filePath = $subFolder .'/' . $img_filename;
                        //         $image_save_path=Storage::disk('s3')->put($image_filePath, file_get_contents($image));
                        //         $img_url = Storage::disk('s3')->url($image_filePath);
                        //         $complaint_attachments_save=ComplaintAttachment::create([
                        //                     'url'=>$img_url,
                        //                     'image_file_name'=>$img_filename,
                        //                     'complaint_id'=>$complaint_obj->id,
                        //                     'status'=>true
                        //                 ]);
                        //         $complaint_img_files[$file_exist]=$complaint_attachments_save->url;
                        //         $file_exist+=1;
                                
                        //         // foreach($request->complaint_img_file as $image) {
                                    
                        //         // }
                        //     }
                        // }
                       
                        // foreach ($request->file('complaint_img_file') as $key => $image)
                        // {
                        //     // Image File Name without Extension
                        //     $img_baseName=pathinfo($image->getClientOriginalName());

                        //     $img_extension=$image->getClientOriginalExtension();

                        //     // Generate a unique filename using the original file's extension
                        //     $filename = $img_baseName['filename'] . '.' . $image->getClientOriginalExtension();

                        //     // Store the file in the sub-folder on S3 with the original filename
                        //     $image_filePath = $subFolder .'/' . $filename;
                            
                        //     $image_save_path=Storage::disk('s3')->put($image_filePath, file_get_contents($image));
                            
                        //     // Optionally, you can get the public URL of the uploaded image
                        //     $img_url = Storage::disk('s3')->url($image_filePath);
                            
                            
                            
                        //     $complaint_attachments_save=ComplaintAttachment::create([
                        //         'url'=>$img_url,
                        //         'image_file_name'=>$filename,
                        //         'complaint_id'=>$complaint_obj->id,
                        //         'status'=>true
                        //     ]);

                        //     $complaint_img_files[$file_exist]=$complaint_attachments_save->url;
                        //     $file_exist+=1;


                        // } 

                    } else {
                        $file_exist=0;
                        $complaint_img_files=[];
                    }
                    

                    if ($complaint_obj->warranty==1)
                    {
                        $warranty_desc="With Warranty";
                    } else {
                        $warranty_desc="No Warranty";
                    }

                    $complaint_images=ComplaintAttachment::where('complaint_id','=',$complaint_obj->id)->get();

                    $complaint_dealer_obj = Vendor::where('id','=', $complaint_obj->vendor_id)->first();

                    $priority_obj=Priority::where('id','=',$complaint_obj->priority_id)->first();
                    $complaint_details=[
                            "id"=>$complaint_obj->id,
                            "complaint_no"=>$complaint_obj->complaint_no,
                            "part_name"=>$complaint_obj->part_name,
                            "description"=>$complaint_obj->description,
                            "make"=>$complaint_obj->make,
                            "model"=>$complaint_obj->model,
                            "part_number"=>$complaint_obj->part_number,
                            "serial_number"=>$complaint_obj->serial_number,
                            "battery_number"=>$complaint_obj->battery_number,
                            "motor_number"=>$complaint_obj->motor_number,
                            "complaint_status"=>$complaint_obj->complaint_status,
                            "chasis_number"=>$complaint_obj->chasis_number,
                            'customer_name'=>$complaint_obj->customer_name,
                            "complaint_date"=>$complaint_obj->created_at->format('d-m-y'),
                            "created_at"=>$complaint_obj->created_at->format('d-m-y'),
                            "updated_at"=>$complaint_obj->updated_at->format('d-m-y'),
                            "comment_by_admin"=>$complaint_obj->comment_by_admin,
                            'priority_name' => $priority_obj->name,
                            'images'=>$complaint_images,
                            'dealer_name'=>$complaint_dealer_obj->name,
                            'warranty'=>$complaint_obj->warranty,
                            'warranty_desc'=>$warranty_desc
                    ];

                    $mailData = [
                        'complaint'=>$complaint_details,
                        'attachment'=>$complaint_images,
                        'complaint_no'=>$complaint_obj->complaint_no,
                        'title' => 'New Complaint, Complaint #:  '.$complaint_obj->complaint_no .' (Date: '.$complaint_obj->created_at->format('d-m-y').')',

                    ];
                    
                    $req_emails=User::where('user_active','=',true)->select('email')->get();
                    if (count($req_emails)>=1)
                    {
                        foreach($req_emails as $email_address_lst)
                        {
                            Mail::to($email_address_lst->email)->send(new NewComplaintEmail($mailData));
                        }

                    }

                    $status_code=200;
                    $data=[
                        "response"=>[
                            "msg"=>"success",
                            "status"=>$status_code,
                            "complaint"=>$complaint_details,
                        ]
                    ];
                return response() -> json($data, $status_code);
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

    //Pending Approvals
    public function pendingapprovals(Request $request)
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

                $complaint_lst_recs=[];
                $complaint_query=Complaint::select('complaint.id as complaint_id','description',
                                    'complaint.priority_id','priority.name as priority_name',
                                    'part_name',
                                    'complaint.created_at',
                                    'complaint.updated_at')
                                    ->leftjoin('priority','priority.id','=','complaint.priority_id')
                                    ->where('complaint_isactive','=',true)
                                    ->where('warranty','=',true)
                                    ->where('approval_chk','=',0)
                                    ->where('complaint_status','=','Pending')
                                    ->orderby('complaint.id','desc')
                                    ->get();

                $status_code=200;
                $data=[
                    "response"=>[
                        "msg"=>"success",
                        "status"=>$status_code,
                        'total_approvals'=>count($complaint_query)
                    ]
                ];
                return response()->json($data, $status_code);



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

    //Complaint List
    public function complaint_lst(Request $request)
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

                $complaint_lst_recs=[];
                $complaint_query=Complaint::select('complaint.id as complaint_id',
                                    'complaint_no',
                                    'part_name',
                                    'description',
                                    'make',
                                    'model',
                                    'chasis_number',
                                    'battery_number',
                                    'motor_number',
                                    'complaint_status',
                                    'complaint.priority_id as priority_id',
                                    'priority.name as priority_name',
                                    'complaint.vendor_id',
                                    'vendor.name as vendor_name',
                                    'complaint.warranty',
                                    'complaint.comment_by_admin',
                                    'complaint.complaint_isactive',
                                    'complaint.user_id',
                                    'complaint.created_at',
                                    'complaint.updated_at')
                                    ->leftjoin('priority','priority.id','=','complaint.priority_id')
                                    ->leftjoin('vendor','vendor.id','=','complaint.vendor_id')
                                    ->where('complaint_isactive','=',true)
                                    ->orderby('priority_lvl')
                                    ->orderby('complaint.id','desc')
                                    ->get();

                $c_i=0;
                foreach($complaint_query as $itm)
                {
                    if ($itm->warranty==true)
                    {
                        $warranty_desc="Yes";
                    } else {
                        $warranty_desc="No";
                    }
                    $priority_obj=Priority::where('id','=',$itm->priority_id)->first();
                    $complaint_lst_recs[$c_i]=[
                        "complaint_id"=>$itm->complaint_id,
                        'complaint_no'=>$itm->complaint_no,
                        "part_name"=>$itm->part_name,
                        "description"=>$itm->description,
                        "make"=>$itm->make,
                        "model"=>$itm->model,
                        'chasis_number'=>$itm->chasis_number,
                        "battery_number"=>$itm->battery_number,
                        "motor_number"=>$itm->motor_number,
                        "complaint_status"=>$itm->complaint_status,
                        "priority_id"=>$itm->priority_id,
                        "priority_name"=>$priority_obj->name,
                        "vendor_id"=>$itm->vendor_id,
                        "vendor_name"=>$itm->vendor_name,
                        "comment_by_admin"=>$itm->comment_by_admin,
                        "complaint_isactive"=>$itm->complaint_isactive,
                        "warranty"=>$itm->warranty,
                        "warranty_desc"=>$warranty_desc,
                        "created_at"=>$itm->created_at->format('d-m-y'),
                        "user_id"=>$itm->user_id
                    ];

                    $c_i+=1;
                }
                $status_code=200;
                $data=[
                    "response"=>[
                        "msg"=>"success",
                        "status"=>$status_code,
                        'data'=>$complaint_lst_recs
                    ]
                ];
                return response()->json($data, $status_code);



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

    //Complaint List According to Vendor
    public function vendor_complaint_lst(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                    'vendor_id'=>'numeric|exists:vendor,id'
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
                $complaint_lst_recs=[];
                $sel_vendor_id=$request->vendor_id;
                $sel_vendor=Vendor::where('id','=',$sel_vendor_id)->first();

                $complaint_query=Complaint::select('complaint.id as complaint_id',
                                            'complaint_no',
                                            'part_name',
                                            'description',
                                            'make',
                                            'model',
                                            'chasis_number',
                                            'battery_number',
                                            'motor_number',
                                            'complaint_status',
                                            'complaint.priority_id as priority_id',
                                            'priority.name as priority_name',
                                            'complaint.vendor_id',
                                            'customer_name',
                                            'vendor.name as vendor_name',
                                            'complaint.warranty',
                                            'complaint.comment_by_admin',
                                            'complaint.complaint_isactive',
                                            'complaint.user_id',
                                            'complaint.created_at',
                                            'complaint.updated_at')
                                            ->leftjoin('priority','priority.id','=','complaint.priority_id')
                                            ->leftjoin('vendor','vendor.id','=','complaint.vendor_id')
                                            ->where('complaint_isactive','=',true)
                                            ->where('complaint.vendor_id','=',$sel_vendor_id)
                                            ->orderby('priority_lvl')
                                            ->orderby('complaint.id','desc')
                                            ->get();

                    $c_i=0;
                    foreach($complaint_query as $itm)
                    {
                        if ($itm->warranty==true)
                        {
                            $warranty_desc="Yes";
                        } else {
                            $warranty_desc="No";
                        }
                        $priority_obj=Priority::where('id','=',$itm->priority_id)->first();
                        $complaint_lst_recs[$c_i]=[
                                                    "complaint_id"=>$itm->complaint_id,
                                                    'complaint_no'=>$itm->complaint_no,
                                                    "part_name"=>$itm->part_name,
                                                    "description"=>$itm->description,
                                                    "make"=>$itm->make,
                                                    "model"=>$itm->model,
                                                    'chasis_number'=>$itm->chasis_number,
                                                    "battery_number"=>$itm->battery_number,
                                                    "motor_number"=>$itm->motor_number,
                                                    "complaint_status"=>$itm->complaint_status,
                                                    "priority_id"=>$itm->priority_id,
                                                    "priority_name"=>$priority_obj->name,
                                                    "vendor_id"=>$itm->vendor_id,
                                                    "vendor_name"=>$itm->vendor_name,
                                                    'customer_name'=>$itm->customer_name,
                                                    "comment_by_admin"=>$itm->comment_by_admin,
                                                    "complaint_isactive"=>$itm->complaint_isactive,
                                                    "warranty"=>$itm->warranty,
                                                    "warranty_desc"=>$warranty_desc,
                                                    "created_at"=>$itm->created_at->format('d-m-y'),
                                                    "user_id"=>$itm->user_id
                        ];
                            
                        $c_i+=1;
                    }
                    $status_code=200;
                    $data=[
                        "response"=>[
                            "msg"=>"success",
                            "status"=>$status_code,
                            "request"=>$request->all(),
                            "vendor"=>$sel_vendor,
                            'data'=>$complaint_lst_recs
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

    public function withdraw_complaint(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'complaint_id'=>'numeric|exists:complaint,id'
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
                $complaint_id=$request->complaint_id;
            
                $complaint_chk=Complaint::where('id','=',$complaint_id)
                                            ->get();
                if (count($complaint_chk)>=1)
                {
                        $complaint_obj=$complaint_chk[0];
                        $complaint_obj->complaint_status="Cancelled";
                        $complaint_obj->save();

                        $status_code=200;
                        $data=[
                            "msg"=>"Complaint ".$complaint_id." Cancelled ",
                            "status"=>$status_code,
                            "request"=>$request->all(),
                            'data'=>[]
                            
                        ];
                        return response()->json($data, $status_code);
                } else {
                        $status_code=400;
                        $data=[
                            "msg"=>"Select Complaint ID ".$complaint_id." not found",
                            "success"=>false,
                            "status"=>$status_code,
                            "request"=>$request->all(),
                            'data'=>[]
                            
                        ];
                        return response()->json($data, $status_code);
                }
            }


            
        } catch (Exception $e) {
            $error_message="With Draw Complaint API Crash. Error: ".$e->getmessage();
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

    public function complaint_details(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                    'complaint_id'=>'required|numeric|exists:complaint,id',
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

                $sel_complaint_id=$request->complaint_id;
                $complaint_query=Complaint::where('complaint_isactive','=',true)->where('id','=',$sel_complaint_id)->get();
                if (count($complaint_query)>=1)
                {

                        $complaint_obj=$complaint_query[0];
                        $complaint_images=ComplaintAttachment::where('complaint_id','=',$complaint_obj->id)->get();

                        $complaint_dealer_obj = Vendor::where('id','=', $complaint_obj->vendor_id)->first();

                        $priority_obj=Priority::where('id','=',$complaint_obj->priority_id)->first();
                        $vendor_obj=Vendor::where('id','=',$complaint_obj->vendor_id)->first();

                        if ($complaint_obj->warranty==1)
                        {
                            $warranty_desc="Yes";
                        } else {
                            $warranty_desc="No";
                        }
                        $itm=$complaint_obj;
                        $complaint_details=[
                            "complaint_id"=>$complaint_obj->id,
                            'complaint_no'=>$complaint_obj->complaint_no,
                            "part_name"=>$complaint_obj->part_name,
                            "description"=>$complaint_obj->description,
                            "make"=>$complaint_obj->make,
                            "model"=>$complaint_obj->model,
                            "chasis_number"=>$complaint_obj->chasis_number,
                            "battery_number"=>$complaint_obj->battery_number,
                            "motor_number"=>$complaint_obj->motor_number,
                            "priority_id"=>$complaint_obj->priority_id,
                            'priority_name'=>$priority_obj->name,
                            "vendor_id"=>$complaint_obj->vendor_id,
                            'dealer_name'=>$complaint_dealer_obj->name,
                            "comment_by_admin"=>$complaint_obj->comment_by_admin,
                            "complaint_isactive"=>$complaint_obj->complaint_isactive,
                            "complaint_status"=>$complaint_obj->complaint_status,
                            "customer_name"=>$complaint_obj->customer_name,
                            'warranty'=>$complaint_obj->warranty,
                            'warranty_desc'=>$warranty_desc,
                            "created_at"=>$complaint_obj->created_at->format('d-m-y'),
                            "updated_at"=>$complaint_obj->updated_at->format('d-m-y'),
                            'images'=>$complaint_images,
                            "user_id"=>$complaint_obj->user_id
                           
                           
                        ];
                        $status_code=200;




                        $data=[
                            "response"=>[
                                "msg"=>"success",
                                "status"=>$status_code,
                                "data"=> [
                                    "complaint"=>$complaint_details,

                                ]


                            ]
                        ];
                        return response() -> json($data, $status_code);
                } else {
                        $status_code=200;
                        $data=[
                            "response"=>[
                                "msg"=>"fail",
                                "status"=>$status_code,
                                "data"=> []
                            ]
                        ];
                        return response() -> json($data, $status_code);
                }
                $complaint_obj=Complaint::create([
                        'part_name'=>$request->part_name,
                        'description'=>$request->description,
                        'make'=>$request->make,
                        'model'=>$request->model,
                        'chasis_number'=>$request->chasis_number,
                        'priority_id'=>$request->priority_id,
                        'complaint_status'=>'D',
                        'complaint_active'=>true
                ]);
                $status_code=200;
                $data=[
                        "response"=>[
                            "msg"=>"success",
                            "status"=>$status_code,
                            "data"=> $complaint_obj
                        ]
                ];
                return response() -> json($data, $status_code);
            }



           
        } catch (Exception $e) {
            $error_message="Get Complaint Details API Crash. Error: ".$e->getmessage();
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



    // Used by Operator Level
    public function update_complaint_sc(Request $request)
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
                    'complaint_id'=>'required|numeric|exists:complaint,id',
                    'complaint_status'=>'required|string',
                    'comment_by_admin'=>'required|string'
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

                    $sel_complaint_id=$request->complaint_id;
                    $sel_complaint_status=$request->complaint_status;
                    $sel_complaint_comment=$request->comment_by_admin;
                    $sel_admin_user_id = $request->user_id;

                    $myComplaint = Complaint::where('id','=',$sel_complaint_id)->update(['complaint_status'=>$sel_complaint_status, 'comment_by_admin'=>$sel_complaint_comment]);
                    // $complaint_query=Complaint::update();

                    $status_code=200;
                    $complaint_obj=Complaint::where('id','=',$sel_complaint_id)->first();
                    $data=[
                        "response"=>[
                            "msg"=>"Updated Status & Comment Successfully for ".$sel_complaint_id."!",
                            "success"=>true,
                            "status"=>$status_code,
                            "data"=>$complaint_obj
                        ]
                    ];
                    return response()->json($data, $status_code);

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
            $error_message="Get Complaint Details API Crash. Error: ".$e->getmessage();
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

    //Used by Manager / Super Admin
    public function approval_update_complaint_sc(Request $request)
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
                    'complaint_id'=>'required|numeric|exists:complaint,id',
                    'complaint_status'=>'required|string',
                    'comment_by_admin'=>'required|string'
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

                    $sel_complaint_id=$request->complaint_id;
                    $sel_complaint_status=$request->complaint_status;
                    $sel_complaint_comment=$request->comment_by_admin;
                    $sel_admin_user_id = $request->user_id;
                    $complaint_obj=Complaint::where('id','=',$sel_complaint_id)->first();

                    $myComplaint = Complaint::where('id','=',$sel_complaint_id)->update(['complaint_status'=>$sel_complaint_status, 'comment_by_admin'=>$sel_complaint_comment, 'approval_chk'=>1,'approval_dt_time'=>Carbon::now()]);
                    // $complaint_query=Complaint::update();

                    // $status_code=200;

                    return response() -> json(['success'=>'Updated Status & Comment Successfully!']);

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
            $error_message="Get Complaint Details API Crash. Error: ".$e->getmessage();
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
