<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AnnoucementRequest;
use App\Http\Requests\EditAnnoucementRequest;
use App\Models\Annoucement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth; 
use App\Models\User;
use Illuminate\Support\Facades\Storage;


class AnnoucementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:api')->only("store","update","destroy","myAnnoucements");
    }

    public function index()
    {
        $annoucements=Annoucement::with("category","galleries","cities","user")->latest()->get();
        return response()->json(["annoucements"=>$annoucements]);
    }

    public function myAnnoucements(){
        $user=$user=Auth::guard('api')->user();
        $annoucements=Annoucement::whereUserId($user->id)->with("category","galleries","cities","user")->latest()->get();
        return response()->json(["annoucements"=>$annoucements]);
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
    public function store(AnnoucementRequest $request)
    {

        $data["title"]=$request->title;
        $data["description"]=$request->description;
        $data["price"]=$request->price;
        $data["category_id"]=$request->category_id;
        $data["user_id"]=Auth::guard('api')->user()->id;

        ini_set('memory_limit', '-1');

        $image = $request->image;
        $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
        \Image::make($image)->resize(420, 240)->save(public_path('storage\images\annoucements\\').$name);
        $data["image"]=$name;

        $annoucement=Annoucement::create($data);

        if($request->gallery){
            $gallery=[];
            foreach($request->gallery as $index=>$item){
                $name = $index.time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                \Image::make($item)->resize(420, 240)->save(public_path('storage\gallery\annoucements\\').$name);
                $gallery[$index]["image"]=$name;
            }
            $annoucement->galleries()->createMany($gallery);
        }
        
        $annoucement->cities()->attach($request->cities);
        if($annoucement){
            return response()->json(["success"=>true]);
        }
        else{
            return response()->json(["success"=>false]);
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
        $annoucement=Annoucement::whereId($id)->with("cities:id","galleries")->first();
        return response()->json(["annoucement"=>$annoucement]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditAnnoucementRequest $request, $id)
    {

        $annoucement=Annoucement::whereId($id)->with("galleries")->first();

        $user=Auth::guard('api')->user();

        if (! Gate::forUser($user)->allows('edit-annoucement', $annoucement)){
            abort(403);
        }

        $data["title"]=$request->title;
        $data["description"]=$request->description;
        $data["price"]=$request->price;
        $data["category_id"]=$request->category_id;
        //$data["user_id"]=Auth::guard('api')->user()->id;

        if($request->image){
            $image = $request->image;
            $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            \Image::make($image)->resize(420, 240)->save(public_path('storage\images\annoucements\\').$name); 
            if(Storage::disk('local')->exists('public/images/annoucements/'.$annoucement->image->basename)){
                Storage::disk('local')->delete('public/images/annoucements/'.$annoucement->image->basename);
            }
            $data["image"]=$name;
        }

        if($request->gallery){

            $annoucement->galleries()->delete();
            foreach($annoucement->galleries as $img){
                if(Storage::disk('local')->exists('public/gallery/annoucements/'.$img->image->basename)){
                    Storage::disk('local')->delete('public/gallery/annoucements/'.$img->image->basename);
                }
            }

            $gallery=[];
            foreach($request->gallery as $index=>$item){
                $name = $index.time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                \Image::make($item)->resize(420, 240)->save(public_path('storage\gallery\annoucements\\').$name);
                $gallery[$index]["image"]=$name;
            }

            $annoucement->galleries()->createMany($gallery);
        }

   

        $success=$annoucement->update($data);
     
        $annoucement->cities()->sync($request->cities);
        if($success){
            return response()->json(["success"=>true]);
        }
        else{
            return response()->json(["success"=>false]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $annoucement=Annoucement::whereId($id)->with("galleries")->first();

        $user=Auth::guard('api')->user();

        if (! Gate::forUser($user)->allows('edit-annoucement', $annoucement)){
            abort(403);
        }

        $annoucement_img=$annoucement->image->basename?$annoucement->image->basename:"";
        if($annoucement->delete()){ 
            $annoucement->cities()->detach();
            $annoucement->galleries()->delete();
            if(Storage::disk('local')->exists('public/images/annoucements/'.$annoucement_img)){
                Storage::disk('local')->delete('public/images/annoucements/'.$annoucement_img);
            } 
            foreach($annoucement->galleries as $img){
                if(Storage::disk('local')->exists('public/gallery/annoucements/'.$img->image->basename)){
                    Storage::disk('local')->delete('public/gallery/annoucements/'.$img->image->basename);
                }
            }
            return response()->json(["success"=>true]);
        }
        else{
            return response()->json(["success"=>false]);
        }
    }
}
