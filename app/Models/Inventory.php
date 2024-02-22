<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $connection = 'mysql';
    protected $fillable = [
                        'part_name',
                        'part_number',
                        'description',
                        'make',
                        'supported_models',
                        'quantity',
                        'status',
                        'price',
                        'manufacture_date',
                        'warranty_period',
                        'warehouse_id'
                    ];
}
