<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFieldResponse extends Model
{
    use HasFactory;
    protected $table='customfield_responses';
    public $timestamps=false;
    protected $fillable=[
        'field_value',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function customfield()
    {
        return $this->belongsTo(CustomField::class);
    }
}
