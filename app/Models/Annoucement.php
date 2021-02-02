<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Annoucement extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden = ['pivot'];
    
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }
    public function cities()
    {
        return $this->belongsToMany('App\Models\City');
    }
    public function getImageAttribute($val){
        return(Storage::disk('local')->exists('public/images/annoucements/'.$val))? (\Image::make(public_path()."\\storage\\images\\annoucements\\".$val)->encode('data-url')):($val);
    }

    public function galleries()
    {
        return $this->hasMany('App\Models\Gallery');
    }

    public function getCreatedAtAttribute($value)
    {
        $phpdate = strtotime( $value );
        $mysqldate = date( "d/m/y h:m:s", $phpdate );
        return $mysqldate;
    }

}
