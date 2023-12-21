<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sell extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    protected $fillable=[
        'request_by',
        'approve_edit'
    ];


     public function customfieldresponse()
    {
        return $this->hasMany(CustomFieldResponse::class,'sell_id');
    }

    public function productcustomfields()
    {
        return $this->hasMany(ProductCustomField::class,'product_id','product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class,'author_id');
    }
        public function bumpresponses()
    {
        return $this->hasMany(BumpResponse::class,'sell_id');
    }
}
?>
