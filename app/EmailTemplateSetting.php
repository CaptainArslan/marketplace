<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateSetting extends Model
{
    use HasFactory;
        protected $table='email_template_settings';
    public $timestamps=false;
    protected $fillable=[
        'name',
    ];
    //protected $with=[];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
