<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherCategory extends Model
{
    use HasFactory;
    protected $table='othercategories';
    public $timestamps=false;
    protected $fillables=[
        'category_name',
        'subcategory_name'
    ];
    public function othercategoryproduct(){
        return $this->hasMany(OtherCategoryProduct::class,'othercategory_id');
    }

}
