<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCustomField extends Model
{
    use HasFactory;
    protected $table='product_customfields';
    public $timestamps=false;
    protected $fillables=[

    ];
protected $with =['customfields'];
 public function product()
    {
        return $this->belongsTo(Product::class);
    }
 public function customfields()
    {
        return $this->belongsTo(CustomField::class,'customfield_id');
    }
}
