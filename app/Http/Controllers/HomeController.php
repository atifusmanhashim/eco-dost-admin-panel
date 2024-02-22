<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Vendor;
use App\Models\Priority;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Exception;
use Redirect;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {


        try {
            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('Ecodost Admin Access Token')->accessToken;
                $request->session()->put('user_name',$user->name);

                return redirect::to('/dashboard');

            } else {
                return Redirect('/login');
            }
        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Root: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }

    }

    //User Dashboard
    public function dashboard(Request $request)
    {

        try {
            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('Ecodost Admin Access Token')->accessToken;
                $request->session()->put('user_name',$user->name);
                $complaint_lst_recs=[];
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
                                    ->where('complaint_isactive','=',true)
                                    ->whereNotIn('complaint_status', ['Resolved','Cancelled'])
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

                // ["Pending", "In process", "Resolved", "Approval"]
                $complaint_status_query=Complaint::select(DB::raw('IFNULL(count(id),0) as total_complaints'),
                                                        DB::raw("IFNULL(count(case when complaint_status='Pending' then id end),0) as total_pending_complaints"),
                                                        DB::raw("IFNULL(count(case when complaint_status='In Process' then id end),0) as total_inprocess_complaints"),
                                                        DB::raw("IFNULL(count(case when complaint_status='Cancelled'   then id end),0) as total_cancelled_complaints"),
                                                        DB::raw("IFNULL(count(case when complaint_status='Resolved'  then id end),0) as total_resolved_complaints"))->get();

                if (count($complaint_status_query)>=1)
                {
                    $complaint_status=$complaint_status_query[0];
                    $total_complaints=$complaint_status->total_complaints;
                    $total_pending_complaints=$complaint_status->total_pending_complaints;
                    $total_inprocess_complaints=$complaint_status->total_inprocess_complaints;
                    $total_cancelled_complaints=$complaint_status->total_cancelled_complaints;
                    $total_resolved_complaints=$complaint_status->total_resolved_complaints;
                } else {
                    $complaint_status=null;
                    $total_complaints=0;
                    $total_pending_complaints=0;
                    $total_inprocess_complaints=0;
                    $total_cancelled_complaints=0;
                    $total_resolved_complaints=0;
                }



                return view('index',compact('complaint_status','complaint_lst_recs'));

                // return view('index');
            } else {
                return Redirect('/login');
            }
        } catch (Exception $e) {
            $error_code="A002";
            $error_msg="Error in Serving Dashboard: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }
    }

     // Users List
    public function users_lst(Request $request)
    {
        try {
            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('Ecodost Admin Access Token')->accessToken;
                $request->session()->put('user_name',$user->name);
                $user_lst_recs=[];
                $user_query=User::select('users.id as user_id','users.name as user_name',
                                    'contact_no','is_auth','user_active',
                                    'role_id','role.name as role_name',
                                    'location_id as location_id',
                                    'location.name as location_name',
                                    'users.created_at',
                                    'users.updated_at')
                                    ->leftjoin('role','role.id','=','users.role_id')
                                    ->leftjoin('location','location.id','=','users.location_id')
                                    ->where('user_active','=',true)
                                    ->orderby('users.id','desc')
                                    ->get();
                $u_i=0;
                foreach($user_query as $itm)
                {
                    $user_status_name = "";
                    if ($itm->user_active == 1)
                    {
                        $user_status_name = "Active";
                    }
                    elseif ($itm->user_active!= 1)
                    {
                        $user_status_name = "Inactive";
                    }

                    $account_status = "";
                    if ($itm->is_auth == true)
                    {
                        $account_status = "Active";
                    }
                    elseif ($itm->is_auth == false)
                    {
                        $account_status = "Suspended";
                    }


                    $user_lst_recs[$u_i]=[
                        "user_id"=>$itm->user_id,
                        "user_name"=>$itm->user_name,
                        "contact_no"=>$itm->contact_no,
                        "role_id"=>$itm->role_id,
                        "role_name"=>$itm->role_name,
                        "location_id"=>$itm->location_id,
                        "location_name"=>$itm->location_name,
                        "user_status"=>$user_status_name,
                        "account_status"=>$account_status,
                        "created_at"=>$itm->created_at->format('d-m-y')
                    ];

                    $u_i+=1;
                }


                return view('users_lst',compact('user_lst_recs'));


            } else {
                return Redirect('/login');
            }
        } catch (Exception $e) {
            $error_code="A002";
            $error_msg="Error in User List: ".$error_code." ".$e->getmessage().". Please check your process or report to Sasta Tareen System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }

    }


    /*Language Translation*/
    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $user = User::find($id);
        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if ($request->file('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            $user->avatar =  $avatarName;
        }

        $user->update();
        if ($user) {
            Session::flash('message', 'User Details Updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return response()->json([
                'isSuccess' => true,
                'Message' => "User Details Updated successfully!"
            ], 200); // Status code here
        } else {
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
            return response()->json([
                'isSuccess' => true,
                'Message' => "Something went wrong!"
            ], 200); // Status code here
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'isSuccess' => false,
                'Message' => "Your Current password does not matches with the password you provided. Please try again."
            ], 200); // Status code
        } else {
            $user = User::find($id);
            $user->password = Hash::make($request->get('password'));
            $user->update();
            if ($user) {
                Session::flash('message', 'Password updated successfully!');
                Session::flash('alert-class', 'alert-success');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Password updated successfully!"
                ], 200); // Status code here
            } else {
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Something went wrong!"
                ], 200); // Status code here
            }
        }
    }
}
