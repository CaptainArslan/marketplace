<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BumpResponse extends Model
{
    use HasFactory;
    protected $table = 'bump_responses';
    public $timestamps = false;
    protected $fillable = [
        'field_value',
    ];
    protected $with = ['bump'];

    public function bump()
    {
        return $this->belongsTo(ProductBump::class, 'bump_id');
    }
    public function sells()
    {
        return $this->belongsTo(Sell::class, 'sell_id');
    }
}
