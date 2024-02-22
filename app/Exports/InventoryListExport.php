<?php

namespace App\Exports;

use App\Models\Inventory;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class InventoryListExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $inventory_lst=Inventory::select(
                                'part_number',
                                'part_name',
                                'make',
                                'supported_models',
                                'quantity',
                                'price',
                                'warranty_period',
                                'warehouse.warehouse_name')
                            ->leftjoin('warehouse','warehouse.id','=','inventory.warehouse_id')
                            ->where('inventory.status','=',1)
                            ->get();
        return $inventory_lst;
    }

    public function headings(): array
    {
        $sel_headings=array(
            'PartNumber',
            'PartName',
            'Make / Origin',
            'Support Models',
            'Quantity',
            'Price',
            'Warranty Period (Days)',
            'WareHouse');
        return $sel_headings;

    }

}
