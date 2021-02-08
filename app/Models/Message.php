<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Message extends Model
{
    use HasFactory;

    protected $table="messages";

    protected $guarded=[];

    public function annoucement()
    {
        return $this->belongsTo('App\Models\Annoucement');
    }

    public function getFromAttribute($value){
            $user=User::find($value);
            return $user;
    }
    public function getToAttribute($value){
        $user=User::find($value);
        return $user;
    }
}
