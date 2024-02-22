<?php

namespace App\Http\Controllers;

use App\Models\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Str;

#Use for Validation
use Illuminate\Support\Facades\Validator;

#Use for Authentication
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use App\Exports\InventoryListExport;
use App\Imports\InventoryListImportCSV;

use Excel;

use AppHelper;

use File;

use Redirect;
class InventoryController extends Controller
{
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
                $inventory_lst_recs=[];
                $inventory_query=Inventory::select('inventory.id as inventory_id',
                                    'inventory.part_name as part_name',
                                    'inventory.part_number as part_number',
                                    'inventory.make as make','inventory.description as description',
                                    'inventory.supported_models as supported_models',
                                    'inventory.quantity as quantity',
                                    'inventory.status as status',
                                    'inventory.price as price',
                                    'inventory.motor_number as motor_number',
                                    'inventory.battery_number as battery_number',
                                    'inventory.manufacture_date as manufacture_date',
                                    'inventory.warranty_period as warranty_period',
                                    'inventory.warehouse_id as warehouse_id')
                                    ->orderby('inventory.id','desc')
                                    ->get();
                $in_i=0;
                foreach($inventory_query as $itm)
                {
                    if ($itm->status==1)
                    {   
                        $status_desc="Active";
                    } else {
                        $status_desc="In-Active";
                    }
                    $inventory_lst_recs[$in_i]=[
                        "inventory_id"=>$itm->inventory_id,
                        "part_name"=>$itm->part_name,
                        "part_number"=>$itm->part_number,
                        "make"=>$itm->make,
                        "description"=>$itm->description,
                        "supported_models"=>$itm->supported_models,
                        "quantity"=>$itm->quantity,
                        "battery_number"=>$itm->battery_number,
                        "motor_number"=>$itm->motor_number,
                        "status"=>$status_desc,
                        "price"=>$itm->price,
                        "manufacture_date"=>$itm->manufacture_date,
                        "warranty_period"=>$itm->warranty_period,
                        "warehouse_id"=>$itm->warehouse_id
                    ];

                    $in_i+=1;
                }
                return view('inventory',compact('inventory_lst_recs'));
            } else {
                return Redirect::to('/login');
            }
        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Root: ".$error_code." ".$e->getmessage().". Please check your process or report to Ecodost System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }
    }

    //Download Inventory / Template
    public function download_template(Request $request)
    {
        //User Dashboard
        try {
            if (Auth::check())
            {
                $user=Auth::user();
                Auth::login($user);

                // $investory_list=download_template();
                // $fp_inventory_lst=fopen(public_path('inventory_list_download_template.csv'),'w');
                // // Loop through file pointer and a line
                // foreach ($investory_list as $fields) {
                //     fputcsv($fp_inventory_lst, $fields);
                // }
                // fclose($fp_inventory_lst);
                // $download_file=public_path('inventory_list_download_template.csv');
                // $file_name='inventory_list_download_template.csv';
                // $headers = ['Content-Type: text/csv'];
               // return response()->download($download_file,$file_name,$headers);
                return Excel::download(new InventoryListExport, 'inventory_list.csv');

            } else {
                return Redirect::to('/login');
            }

        } catch (Exception $e) {
            $error_code="A001";
            $error_msg="Error in Root: ".$error_code." ".$e->getmessage().". Please check your process or report to Ecodost System Admin ";
            return view('error',compact('error_msg'))->with('alert-danger', $error_msg);
        }
    }

    public function import_inventory(Request $request)
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
                $import_validator = Validator::make($request->all(), [
                    'excel_file'=>'required|mimes:csv,txt'
                ]);
                if ($import_validator->fails()) {
                    $status_code=400;
                    $data=[
                        "response"=>[
                            "msg"=>implode(",",$import_validator->messages()->all()),
                            "status"=>$status_code
                        ]
                    ];
                    return response() -> json($data, $status_code);
                } else {
                    $total_rows=0;
                    $total_success_rows_imported=0;
                    $total_insert_rows_imported=0;
                    $total_update_rows_imported=0;
                    $total_error_rows_imported=0;
                    if ($request->hasFile('excel_file'))
                    {
                        //Saving file
                        $path = $request->file('excel_file')->getRealPath();
                        $upload_option=$request->upload_option;
                        $restrict_chars=array("+","@","=","-","cmd","*");
                        $replace_chars=array("'+","'@","'=","-","'cmd","'*");

                        // Getting the extension of file
                        $extension = File::extension($request->file('excel_file')->getClientOriginalName());
                        if ($extension=="csv")
                        {
                            $import_file = file($request->file('excel_file')->getRealPath());
                            if (count($import_file) > 1)
                            {
                                config(['excel.import.startRow' => 2]);
                                $create_data_file = fopen(base_path('upload_inventory/upload_inventory_rep.csv'),'w');
                                $import_csv_data = array_map('str_getcsv', $import_file);
                                foreach ($import_csv_data as $fields) {
                                    fputcsv($create_data_file, str_replace($restrict_chars,$replace_chars,$fields));
                                }
                                fclose($create_data_file);

                                $new_import_file=base_path('upload_inventory/upload_inventory_rep.csv');
                                $new_import_csv_data = array_map('str_getcsv', file($new_import_file));

                                $inventory_import=new InventoryListImportCSV();

                                $inventory_data=Excel::import($inventory_import, base_path('upload_inventory/upload_inventory_rep.csv'));
                                $data_type="Inventory List";
                                $total_rows=$inventory_import->getRowCount();
                                $total_success_rows_imported=$inventory_import->getSuccessRowCount();
                                $total_insert_rows_imported=$inventory_import->getInsertRowCount();
                                $total_update_rows_imported=$inventory_import->getUpdateRowCount();
                                $total_error_rows_imported=$inventory_import->getErrorRowCount();

                                if ($total_rows>=1)
                                {
                                    if ($total_error_rows_imported==0)
                                    {
                                        $status_code=200;
                                        $data=[
                                            "response"=>[
                                                "msg"=>"Inventory List Impprted Successfully. Total Rows Imported: ".$total_rows." (Insert: ".$total_insert_rows_imported.", Update: ".$total_update_rows_imported.")",
                                                "success"=>true,
                                                "status"=>$status_code,
                                                "total_rows"=>$total_rows,
                                                "total_insert_rows"=>$total_insert_rows_imported,
                                                "total_update_rows"=>$total_update_rows_imported,
                                                "total_error_rows"=>$total_error_rows_imported,
                                            ]
                                        ];
                                    } else {
                                        $status_code=200;
                                        $data=[
                                            "response"=>[
                                                "msg"=>"Inventory List Impprted UnSuccessfully.  Total Rows : ".$total_rows.", Total Error Rows: ".$total_error_rows_imported." ",
                                                "success"=>false,
                                                "status"=>$status_code,
                                                "total_rows"=>$total_rows,
                                                "total_insert_rows"=>$total_insert_rows_imported,
                                                "total_update_rows"=>$total_update_rows_imported,
                                                "total_error_rows"=>$total_error_rows_imported,
                                            ]
                                        ];
                                    }
                                } else {
                                    $status_code=200;
                                    $data=[
                                            "response"=>[
                                                "msg"=>"CSV File contains no data rows",
                                                "success"=>false,
                                                "status"=>$status_code,
                                                "total_rows"=>$total_rows,
                                                "total_insert_rows"=>$total_insert_rows_imported,
                                                "total_update_rows"=>$total_update_rows_imported,
                                                "total_error_rows"=>$total_error_rows_imported,
                                            ]
                                    ];
                                    return response() -> json($data, $status_code);
                                }


                                return response()->json($data, $status_code);
                            } else {
                                $status_code=200;
                                $data=[
                                        "response"=>[
                                            "success"=>false,
                                            "msg"=>"Invalid File Sslected",
                                            "status"=>$status_code,
                                            "total_rows"=>$total_rows,
                                            "total_insert_rows"=>$total_insert_rows_imported,
                                            "total_update_rows"=>$total_update_rows_imported,
                                            "total_error_rows"=>$total_error_rows_imported,
                                        ]
                                ];
                                return response() -> json($data, $status_code);

                            }

                        } else {
                            $status_code=200;
                            $data=[
                                "response"=>[
                                    "msg"=>"CSV File Not Selected",
                                    "success"=>false,
                                    "status"=>$status_code,
                                    "total_rows"=>$total_rows,
                                    "total_insert_rows"=>$total_insert_rows_imported,
                                    "total_update_rows"=>$total_update_rows_imported,
                                     "total_error_rows"=>$total_error_rows_imported,
                                ]
                            ];
                            return response() -> json($data, $status_code);

                        }

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
}
