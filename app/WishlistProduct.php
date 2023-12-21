<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistProduct extends Model
{
    use HasFactory;
        protected $table='wishlistproducts';
    public $timestamps=false;
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
