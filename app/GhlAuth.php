<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GhlAuth extends Model
{
    use HasFactory;
      protected $table='ghl_auths';
    protected $guarded=[];
    protected $fillables=[
        'access_token',
        'location_id',
        'refresh_token'
    ];
}
