<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;
    protected $table='customfields';
    public $timestamps=false;
    protected $fillable=[
        'name',
    ];
protected $with=['customfielditem'];
 public function productcustomfields()
    {
        return $this->hasMany(ProductCustomField::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function customfielditem()
    {
        return $this->hasMany(CustomfieldItem::class, 'customfield_id');
    }


}
