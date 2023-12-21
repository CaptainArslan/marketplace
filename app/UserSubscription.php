<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;
    protected $table = 'user_subscriptions';
    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function subscriptions()
    {
        return $this->belongsTo(Subscription::class,'sub_id');
    }
}
