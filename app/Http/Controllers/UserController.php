<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\Models\User; 
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;


class userController extends Controller 
{
public $successStatus = 200;
/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
            $success['role'] = Auth()->user()->role;
            $success['name'] = Auth()->user()->name;
            $success['email'] = Auth()->user()->email;
            $success['mobile'] = Auth()->user()->mobile;
            $success['avatar'] = Auth()->user()->avatar;
            $success['id'] =  $user->id;
            return response()->json(['success' => $success], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
/** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|min:3', 
            'email' => 'required|email|unique:users', 
            'password' => 'required', 
            'avatar'=>'required',
            'confirm' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
             return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']);
        

        $image = $request->avatar;
        $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
        \Image::make($image)->resize(420, 240)->save(public_path('storage\avatars\\').$name);
        $input["avatar"]=$name;
        
        
        $user = User::create($input); 
        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
        $success['name'] =   $user->name;
        $success['avatar'] = $user->avatar;
        $success['email'] =  $user->email;
        $success['mobile'] =  $user->mobile;
        $success['id'] =  $user->id;
        $user->profile()->create();
        return response()->json(['success'=>$success], $this-> successStatus); 
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|min:3', 
            'email' => 'required',  
            'avatar'=>'required', 
        ]);
        if ($validator->fails()) { 
             return response()->json(['errors'=>$validator->errors()], 401);            
        }
        $users=collect(User::where("id","!=",$id)->get());
        /*$filtered = $users->filter(function ($value, $key) use($request,$id){
            return (($value->email == $request->email ) && ($value->id !=$id));
        });*/

        if($users->contains('email', $request->email)){
            return response()->json(["error"=>"Email déja utilisé"]);
        }
        

        $user=User::whereId($id)->first();

        $data["name"]=$request->name;
        $data["email"]=$request->email;
        $data["mobile"]=$request->mobile;

        if($request->avatar){
            $image = $request->avatar;
            $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            \Image::make($image)->resize(420, 240)->save(public_path('storage\avatars\\').$name); 
            if($user->avatar){
                if(Storage::disk('local')->exists('public/avatars/'.$user->avatar->basename)){
                    Storage::disk('local')->delete('public/avatars/'.$user->avatar->basename);
                }
            }       
            $data["avatar"]=$name;
        }

        $user->update($data);
    
        $success['name'] =$user->name;
        $success['email'] =$user->email;
        $success['mobile'] =$user->mobile;
        $success['avatar'] =$user->avatar;

        return response()->json(['success' => $success]); 
     

    }
    /*public function getNotifications(){
        $user=User::find(1);
        $unread=Notification::whereNull("read_at")->orderBy("created_at")->get();
        $notifications=Notification::orderBy("created_at","desc")->get();

        return response()->json(['notifications'=>$notifications,'unread'=>$unread]);
    }

    public function markAsRead($id){
        $notification=Notification::whereId($id)->first();
        $notification->markAsRead();
        $notifications=Notification::orderBy("created_at","desc")->get();
        $unread=Notification::whereNull("read_at")->orderBy("created_at")->get();

        return response()->json(['notifications'=>$notifications,'unread'=>$unread]);
    }
    
    public function deleteNotif($id){
        $notification=Notification::whereId($id)->first();
        $notification->delete();
        $notifications=Notification::orderBy("created_at","desc")->get();
        $unread=Notification::whereNull("read_at")->orderBy("created_at")->get();
        return response()->json(['notifications'=>$notifications,'unread'=>$unread]);
    }*/
   
}