<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth; 


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth:api')->only("store","update","destroy");
    }
    public function index()
    {
        return response()->json(["success"=>true,"categories"=>Category::orderBy("created_at")->get()]);
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
    public function store(CategoryRequest $request)
    {
        $user=Auth::guard('api')->user();
        $response = Gate::forUser($user)->inspect('edit-category');
        
        if (! $response->allowed()){
            return $response->message();
        }
        else{
            $data["name"]=$request->name;
            if($request->image){
                $image = $request->image;
                $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                \Image::make($image)->resize(420, 240)->save(public_path('storage\images\categories\\').$name);
                $data["image"]=$name;
            }
          
            $category=Category::create($data);

            if($category){
                return response()->json(["success"=>true]);
            }
            else{
                return response()->json(["success"=>false]);
            }
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
        $category=Category::whereId($id)->first();
        return response()->json(["category"=>$category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        $user=Auth::guard('api')->user();
        $response = Gate::forUser($user)->inspect('edit-category');
        
        if (! $response->allowed()){
            return $response->message();
        }

        else{
            $category=Category::whereId($id)->first();

            $data["name"]=$request->name;

            if($request->image){
                $image = $request->image;
                $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                \Image::make($image)->resize(420, 240)->save(public_path('storage\images\categories\\').$name); 
                if($category->image){
                    if(Storage::disk('local')->exists('public/images/categories/'.$category->image->basename)){
                        Storage::disk('local')->delete('public/images/categories/'.$category->image->basename);
                    }
                }       
                $data["image"]=$name;
            }

            $success=$category->update($data);
        
            if($success){
                return response()->json(["success"=>true]);
            }
            else{
                return response()->json(["success"=>false]);
            }
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
        $user=Auth::guard('api')->user();
        $response = Gate::forUser($user)->inspect('edit-category');
        
        if (! $response->allowed()){
            return $response->message();
        }

        else{
            $category=Category::whereId($id)->first();
            $category_img=$category->image->basename?$category->image->basename:"";
            if($category->delete()){ 
                if(Storage::disk('local')->exists('public/images/categories/'.$category_img)){
                    Storage::disk('local')->delete('public/images/categories/'.$category_img);
                } 
                return response()->json(["success"=>true]);
            }
            else{
                return response()->json(["success"=>false]);
            }
        }   
    }
}
