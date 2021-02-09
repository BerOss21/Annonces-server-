<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index($id){
        $user=User::find($id);
        $notifications=$user->notifications;
        $unreadNotifications=$user->unreadNotifications;
        return response()->json(["notifications"=>$notifications,"unreadNotifications"=>$unreadNotifications]);
    }

    public function makeAsRead($id,$receiver=null){
        $user=User::find($id);
        if($receiver){
            foreach ($user->unreadNotifications as $notification) {
                if($notification->data["message"]["from"]["id"]==$receiver){
                    $notification->update(['read_at' => now()]);
                }    
            }
            return response()->json(["success"=>true]);
        }   
        else{
            $user->unreadNotifications()->update(['read_at' => now()]);
            return response()->json(["success"=>true]);
        }        
    }

    public function makeNotificationAsRead($id){
        Notification::where("id",$id)->update(['read_at' => now()]);
        return response()->json(["success"=>true]);        
    }
}
