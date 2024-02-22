<?php

namespace App\Imports;

use App\Models\Inventory;
use App\Models\Vendor;
use App\Models\Warehouse;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;

#Use for Validation
use Illuminate\Support\Facades\Validator;

use Exceptiom;
use DateTime;

use Illuminate\Support\Carbon as Carbon;
class InventoryListImportCSV implements ToModel,WithStartRow
{
    use Importable;

    private $rows = 0;
    private $success_rows=0;
    private $insert_rows=0;
    private $update_rows=0;
    private $error_rows=0;
    private $error_msgs=[];
    // public function startRow(): int
    // {
    //     return 2;
    // }
    public function startRow(): int
    {
        return 2;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        try {

            if (!empty($row[0]))
            {
                $part_number=$row[0];
                if (!empty($row[1]))
                {
                    $part_name=$row[1];
                    $inventory_chk=Inventory::where('part_number','=',$part_number)
                                            ->where('part_name',$part_name)
                                            ->get();
                    if(count($inventory_chk)>0)
                    {
                        $inventory_id=$inventory_chk[0]->id;
                        $inventory_flag=true;
                    } else {
                        $inventory_id=0;
                        $inventory_flag=false;
                    }

                    $make=$row[2];
                    $supported_models=$row[3];

                    if(is_numeric($row[4]))
                    {
                        $quantity=$row[4];
                    } else {
                        $quantity=0;
                    }

                    if(is_numeric($row[5]))
                    {
                        $price=$row[5];
                    } else {
                        $price=0;
                    }

                    if(is_numeric($row[6]))
                    {
                        $warranty_period=$row[6];
                    } else {
                        $warranty_period=0;
                    }

                    if (array_key_exists(7,$row))
                    {
                        $warehouse=$row[7];
                    } else {
                        $warehouse=null;
                    }

                    if (!empty(trim($warehouse)))
                    {
                        $warehouse_chk=Warehouse::where('warehouse_name','like','%'.trim($warehouse).'%')->get();
                        if (count($warehouse_chk)>0)
                        {
                            $warehouse_obj=$warehouse_chk[0];
                            $warehouse_id=$warehouse_obj->id;
                        } else {
                            $warehouse_obj=Warehouse::create(['warehouse_name'=>trim($warehouse)]);
                            $warehouse_id=$warehouse_obj->id;
                        }
                    } else {
                        $warehouse_id=null;
                    }

                    if ($inventory_id==0)
                    {
                        $inventory_obj=Inventory::create([
                            'part_name'     => $part_name,
                            'part_number'   =>$part_number,
                            'description'=> $part_name,
                            'make'=> $make,
                            'supported_models'=> $supported_models,
                            'quantity'=> $quantity,
                            'price'=>$price,
                            'warranty_period'=>$warranty_period,
                            'warehouse_id'=>$warehouse_id,
                            'status'=>true
                        ]);
                        print_r($inventory_obj);
                        ++$this->insert_rows;
                    } else {
                       
                        $inventory_obj=Inventory::where('id','=',$inventory_id)->update([
                            'part_name'     => $part_name,
                            'part_number'=>$part_number,
                            'description'=> $part_name,
                            'make'=> $make,
                            'supported_models'=> $supported_models,
                            'quantity'=> $quantity,
                            'price'=>$price,
                            'warranty_period'=>$warranty_period,
                            'warehouse_id'=>$warehouse_id,
                            'status'=>true,
                        ]);
                        
                        ++$this->update_rows;
                    }
                    ++$this->success_rows;
                } else {
                    $this->error_msgs[$this->error_rows]="Empty Part Name at row".$this->rows.". Part Name should be defined properly. ";
                    $part_name=0;
                }

                
                    

                
            } 
            
            // if (!empty($row[0]))
            // {
            //     if(is_numeric($row[0]))
            //     {
                   




                  

            //     }
            // } else {
            //     $inventory_obj=Inventory::create([
            //         'part_name'     => $part_name,
            //         'part_number'   =>$part_number,
            //         'description'=> $description,
            //         'make'=> $make,
            //         'battery_number'=>$battery_num,
            //         'motor_number'=>$motor_num,
            //         'supported_models'=> $supported_models,
            //         'quantity'=> $quantity,
            //         'status'=>true,
            //         'price'=>$price,
            //         'manufacture_date'=>Carbon::now(),
            //         'warranty_period'=>$warranty_period,
            //         'warehouse_id'=>$warehouse_id
            //     ]);
            //     ++$this->insert_rows;
            // }

        } catch (Exception $e) {
            $this->error_msgs[$this->error_rows]=$e->getmessage();
            ++$this->error_rows;
            print_r($e->getmessage());
        }
        ++$this->rows;







    }
    public function getRowCount(): int
    {
        return $this->rows;
    }
    public function getSuccessRowCount(): int
    {
        return $this->success_rows;
    }
    public function getInsertRowCount(): int
    {
        return $this->insert_rows;
    }
    public function getUpdateRowCount(): int
    {
        return $this->update_rows;
    }
    public function getErrorRowCount(): int
    {
        return $this->error_rows;
    }
    public function getErrorMsgs(): array
    {
        return $this->error_msgs;
    }
}
