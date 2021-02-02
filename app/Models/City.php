<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden = ['pivot'];
    public function annoucements()
    {
        return $this->belongsToMany('App\Model\Anoucement');
    }
}
