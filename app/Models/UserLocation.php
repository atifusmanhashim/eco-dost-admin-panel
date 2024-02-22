<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $table = 'location';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $connection = 'mysql';
    protected $fillable = [
                        'name'
                    ];
}
