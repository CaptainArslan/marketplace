<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBump extends Model
{
    use HasFactory;
    protected $table='product_bumps';
    public $timestamps=false;
    protected $fillables=[
        'name',
        'price',
        'min_quantity',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function bumpresponses()
    {
        return $this->hasMany(BumpResponse::class);
    }

}
