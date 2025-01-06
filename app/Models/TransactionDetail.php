<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    public $timestamps = false; 

    protected $fillable = [
        'transaction_id',
        'service_id',
        'quantity',
        'price'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
