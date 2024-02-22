<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $table = 'complaint';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $connection = 'mysql';
    protected $fillable = [
                        'complaint_no',
                        'part_name',
                        'description',
                        'make',
                        'model',
                        'chasis_number',
                        'battery_number',
                        'part_number',
                        'serial_number',
                        'motor_number',
                        'priority_id',
                        'vendor_id',
                        'warranty',
                        'complaint_status',
                        'comment_by_admin',
                        'complaint_isactive',
                        'user_id',
                        'customer_name'
                    ];
}
