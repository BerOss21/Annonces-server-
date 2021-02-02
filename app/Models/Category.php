<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function annoucements()
    {
        return $this->hasMany('App\Model\Anoucement');
    }
    public function getImageAttribute($val){
        if($val){
            return(Storage::disk('local')->exists('public/images/categories/'.$val))? (\Image::make(public_path()."\\storage\\images\\categories\\".$val)->encode('data-url')):($val);
        }
        else{
            return $val;
        }
       
    }
}
