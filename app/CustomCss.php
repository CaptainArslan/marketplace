<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomCss extends Model
{
    use HasFactory;
    protected $table = 'customcss';
    public $timestamps = false;
    protected $fillable = [
        'styletag',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
