<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $table = 'subscriptions';
    public $timestamps = false;
    public function usersubscription()
    {
        return $this->hasMany(UserSubscription::class);
    }
    public function notifixations()
    {
        return $this->hasMany(Notification::class);
    }


}
