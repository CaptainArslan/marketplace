<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'tag' => 'array',
        'category_details' => 'array',
        'screenshot' => 'array',
    ];

    protected $hidden = [
        'shareable_link',
        'server',

    ];

    protected $with = [
        'user',
        // 'sells'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function meeting()
    {
        return $this->hasMany(BuyerSellerMeeting::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function sells()
    {
        return $this->hasMany(Sell::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function wishlists()
    {
        return $this->hasMany(WishlistProduct::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    public function bumps()
    {
        return $this->hasMany(ProductBump::class);
    }
    public function othercategoriesproduct()
    {
        return $this->hasMany(OtherCategoryProduct::class);
    }
    public function productcustomfields()
    {
        return $this->hasMany(ProductCustomField::class);
    }
    public function customfieldresponses()
    {
        return $this->hasMany(CustomFieldResponse::class);
    }
}
