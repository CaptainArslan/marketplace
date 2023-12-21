<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldBump extends Model
{
    use HasFactory;
    protected $table = 'sold_bumps';
    public $timestamps = false;
    protected $fillable = [
        'pages',
    ];

    public function bumps()
    {
        return $this->belongsTo(Bump::class,'bump_id');
    }
    public function sells()
    {
        return $this->belongsTo(Order::class,'sell_id');
    }
}
