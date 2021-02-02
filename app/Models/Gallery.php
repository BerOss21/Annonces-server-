<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden = ['pivot'];
    public function annoucements(){
        return $this->belongsTo("App\Models\Annoucement");
    }

    public function getImageAttribute($val){
        return(Storage::disk('local')->exists('public/gallery/annoucements/'.$val))? (\Image::make(public_path()."\\storage\\gallery\\annoucements\\".$val)->encode('data-url')):($val);
    }
}
