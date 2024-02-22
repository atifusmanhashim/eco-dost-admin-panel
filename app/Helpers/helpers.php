<?php


use App\Models\Inventory;
use App\Models\Complaint;
use App\Models\Vehicle;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

function convertYmdToMdy($date)
{
    return Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
}

function validate_date($date)
{
    try {
        $dt=Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function basic_complaint_directory()
{
    return public_path('complaint_attachments');
}

function download_template()
{
    $sel_headings=['ItemId',
                        'PartName',
                        'PartNumber',
                        'Make / Origin',
                        'Support Models',
                        'Quantity',
                        'Price',
                        'ManufacturingDate',
                        'Warranty Period (Days)',
                        'WareHouse'];

    $inventory_lst=Inventory::select('inventory.id',
                        'part_name',
                        'part_number',
                        'description',
                        'make',
                        'supported_models',
                        'quantity',
                        'price',
                        DB::raw('date_format(manufacture_date,"%Y-%m-%d") as manufacture_date'),
                        'warranty_period',
                        'warehouse.warehouse_name')
                    ->leftjoin('warehouse','warehouse.id','=','inventory.warehouse_id')
                    ->where('inventory.status','=',1)
                    ->get();
    $res=[];
    $res[0]=$sel_headings;
    if (count($inventory_lst) > 0) {
        $data=array_merge($res,$inventory_lst->toArray());
    } else {
        $data=$res;
    }

    return $data;


}

function complaint_status_lst()
{
    return json_encode(array("Pending", "In Process", "Resolved", "Rejected","Send for Approval","Approved"));
}

//Warranty Checker Function
function warranty_check($sel_part_number,$sel_chasis_number)
{
    if ((!empty($sel_part_number)) && (!empty($sel_chasis_number)))
    {
        $part_number_warranty_days_chk=Inventory::select('part_name','part_number','warranty_period')
                                                ->where('part_number','=',$sel_part_number)
                                                ->whereNotNull('warranty_period')
                                                ->where('warranty_period','>=',1)
                                                ->get();

        $chasis_number_pur_date_chk=Vehicle::select('vehicle_name','vehicle_type','vehicle_chasis_number','vehicle_date_purchase as purchase_date')
                ->where('vehicle_chasis_number','=',$sel_chasis_number)
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
                $warranty_status=1;
            } else {
                $warranty_status=0;
            }

        } else {
            $warranty_status=0;
        }
    } else {
        $warranty_status=0;
    }

    return $warranty_status;
}