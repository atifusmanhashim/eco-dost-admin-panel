<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicle';
    protected $primaryKey = 'vehicle_id';
    public $incrementing = true;
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $connection = 'mysql';
    protected $fillable = [
                        'vehicle_name',
                        'vehicle_serial_number',
                        'vehicle_date_purchase',
                        'vehicle_chasis_number',
                        'vehicle_type',
                        'vehicle_make',
                        'vehicle_model',
                        'vehicle_battery_number',
                        'vehicle_motor_number',
                        'vehicle_customer_name',
                        'vehicle_isactive'
                    ];
}
