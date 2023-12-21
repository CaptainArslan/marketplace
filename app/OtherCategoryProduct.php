<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherCategoryProduct extends Model
{
    use HasFactory;
    protected $table='othercategory_products';
    public $timestamps=false;

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function othercategories(){
        return $this->belongsTo(OtherCategory::class);
    }
}
