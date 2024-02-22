<?php

namespace App\Exports;

use App\Models\Vehicle;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class VehicleListExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Vehicle::select( 'vehicle_name',
                                'vehicle_serial_number',
                                'vehicle_chasis_number',
                                'vehicle_battery_number',
                                'vehicle_motor_number',
                                'vehicle_type',
                                'vehicle_make',
                                'vehicle_model',
                                'vehicle_date_purchase',
                                'vehicle_customer_name')
                            ->where('vehicle_isactive','=',true)->get();

    }

    public function headings(): array
    {
        $sel_headings=array(
            'VehicleName',
            'VehicleSerialNumber',
            'VehicleChasisNumber',
            'BatteryNumber',
            'VehicleMotorNumber',
            'VehicleType',
            'Make / Origin',
            'Model',
            'Purchase Date (year-month-date)',
            'Customer');
        return $sel_headings;

    }
}
