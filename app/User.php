<?php

namespace App;

use Carbon\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'exp' => Carbon::now()->addWeek(1)->timestamp, // Set token expiration to 30 days from now
        ];
    }

    use Notifiable, HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guarded = ['id'];
    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'level_id',
        'top_author',
        'email',
        'country_code',
        'mobile',
        'ref_by',
        'balance',
        'earning',
        'total_rating',
        'total_response',
        'avg_rating',
        'password',
        'image',
        'cover_image',
        'description',
        'address',
        'status',
        'ev',
        'sv',
        'ver_code',
        'ver_code_send_at',
        'ts',
        'tv',
        'tsc',
        'provider',
        'provider_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'level' => 'object',
        'ver_code_send_at' => 'datetime'
    ];

    protected $data = [
        'data' => 1
    ];



    public function login_logs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status', '!=', 0);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status', '!=', 0);
    }

    public function products()
    {
        return $this->hasMany(Product::class)->where('status', 1);
    }
    public function customfields()
    {
        return $this->hasMany(CustomField::class);
    }
    public function tempProducts()
    {
        return $this->hasMany(TempProduct::class)->where('status', 1);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function levell()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function buy()
    {
        return $this->hasMany(Sell::class);
    }

    public function sell()
    {
        return $this->hasMany(Sell::class, 'author_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'author_id');
    }

    public function myOrder()
    {
        return $this->hasMany(Order::class, 'order_number');
    }

    public function orderBuy()
    {
        return $this->hasMany(Order::class);
    }
    public function meetings()
    {
        return $this->hasMany(BuyerSellerMeeting::class);
    }
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function existedRating($id)
    {
        return $this->ratings->where('product_id', $id)->first();
    }
    public function usersubscription()
    {
        return $this->hasMany(UserSubscription::class);
    }

    // SCOPES

    public function getFullnameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function scopeBanned()
    {
        return $this->where('status', 0);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', 0);
    }

    public function scopeSmsUnverified()
    {
        return $this->where('sv', 0);
    }
    public function scopeEmailVerified()
    {
        return $this->where('ev', 1);
    }

    public function scopeSmsVerified()
    {
        return $this->where('sv', 1);
    }
}
