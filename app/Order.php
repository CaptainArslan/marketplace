<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'code', 'author_id', 'product_id', 'license', 'support', 'support_time', 'support_fee', 'product_price', 'total_price'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
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
        return $this->hasMany(BumpResponse::class);
    }
}
