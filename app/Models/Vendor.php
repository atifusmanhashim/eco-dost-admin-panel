<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'vendor';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $connection = 'mysql';
    protected $fillable = [
                        'company_name',
                        'name',
                        'phone_no',
                        'password',
                        'address',
                        'vendor_isactive',
                        'vendor_type_id',
                        'city_id'
                    ];
}
