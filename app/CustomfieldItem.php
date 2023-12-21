<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomfieldItem extends Model
{
    use HasFactory;
    protected $table = 'customfield_items';
    public $timestamps = false;
    protected $fillable = [
        'label',
    ];
    public function customfield(){
        return $this->belongsto(CustomField::class);
    }
}
