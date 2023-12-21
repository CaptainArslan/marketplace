<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerSellerMeeting extends Model
{
    use HasFactory;
    protected $table = 'buyer_seller_meetings';
    public $timestamps = false;
    protected $fillable = [
        'agenda',
        'meeting_time',
        'meeting_date',
        'meeting_link',
        'meeting_status',
        'status'
    ];
    //protected $with=[];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productmeeting()
    {
        return $this->belongsTo(Product::class);
    }
}
