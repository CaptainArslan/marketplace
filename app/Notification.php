<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';

    //protected $with=[];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function products()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
    public function selling()
    {
        return $this->belongsTo(Sell::class,'sell_id');
    }
    public function subscription()
    {
        return $this->belongsTo(Subscription::class,'subs_status');
    }
}
?>
