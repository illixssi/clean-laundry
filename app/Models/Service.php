<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'unit',
        'price',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at'
    ];
}
