<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Annoucement;
use Illuminate\Http\Request;
use App\Http\Requests\MessageRequest;
use Illuminate\Support\Facades\Auth; 
use App\Notifications\MessageNotification;


class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:api')->only("store","getMessages");
    }

    public function index()
    {
        //
    }

    public function getMessages($annoucement_id,$user_id,$prop=null){
        $messages=Message::where("annoucement_id",$annoucement_id)->where(function($query) use($user_id) {
            $query->where("from",$user_id)
                  ->orwhere("to",$user_id);
        })->get();
        
        $list=$prop?Message::where("annoucement_id",$annoucement_id)->where(function($query) use($user_id) {
            $query->where("from",$user_id)
                  ->orwhere("to",$user_id);
        })->select("from","to")->get():"";    
        return response()->json(["messages"=>$messages,"list"=>$list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MessageRequest $request)
    {
        $message=Message::create([
            "annoucement_id"=>$request->annoucement_id,
            "from"=>Auth::guard('api')->user()->id,
            "to"=>$request->to,
            "content"=>$request->content,
        ]);
        if($message){
            $user=User::find($request->to);
            $user->notify(new MessageNotification($message));
            return response()->json(["success"=>$message]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
