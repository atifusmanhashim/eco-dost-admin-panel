<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use App\Models\UserLocation;
use App\Models\User;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Str;

#Use for Validation
use Illuminate\Support\Facades\Validator;

#Use for Authentication
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Redirect;

use Exception;

class AddUserController extends Controller
{
    
    // Add User Page
    public function index(Request $request)
    {
        $available_roles=[];
        $available_locations=[];
        $available_roles_query=UserRole::select('role.id as role_id','role.name as role_name')->get();
        $available_location_query=UserLocation::select('location.id as location_id','location.name as location_name')->get();
        $u_i=0;
        $l_i=0;
        foreach($available_roles_query as $itm)
        {
            $available_roles[$u_i]=[
                "role_id"=>$itm->role_id,
                "role_name"=>$itm->role_name
            ];
            $u_i += 1;
        }

        foreach($available_location_query as $itm)
        {
            $available_locations[$l_i]=[
                "location_id"=>$itm->location_id,
                "location_name"=>$itm->location_name
            ];
            $l_i += 1;
        }

        return view('adduser',compact('available_roles','available_locations'));
    }

    // Edit User Page
    public function edituser($id)
    {

        //User Dashboard
        try {

            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);
                $access_token = $user->createToken('Ecodost Admin Access Token')->accessToken;


                $available_roles=[];
                $available_locations=[];
                $available_roles_query=UserRole::select('role.id as role_id','role.name as role_name')->get();
                $available_location_query=UserLocation::select('location.id as location_id','location.name as location_name')->get();
                $u_i=0;
                $l_i=0;
                foreach($available_roles_query as $itm)
                {
                    $available_roles[$u_i]=[
                        "role_id"=>$itm->role_id,
                        "role_name"=>$itm->role_name
                    ];
                    $u_i += 1;
                }

                foreach($available_location_query as $itm)
                {
                    $available_locations[$l_i]=[
                        "location_id"=>$itm->location_id,
                        "location_name"=>$itm->location_name
                    ];
                    $l_i += 1;
                }

                $sel_user_id=$id;
                $user_data=User::where('id','=',$sel_user_id)->where('user_active','=',true)->first();

                return view('edituser',compact('sel_user_id','user_data', 'available_roles', 'available_locations'));
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
