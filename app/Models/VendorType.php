<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorType extends Model
{
    protected $table = 'vendor_type';
    protected $primaryKey = 'vendor_type_id';
    public $incrementing = true;
    public $timestamps = true;
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $connection = 'mysql';
    protected $fillable = [
                        'vendor_type_name',
                        'vendor_type_isactive'
                    ];
}
