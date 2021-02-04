<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Http\Requests\CityRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth; 

class CityController extends Controller
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
        return response()->json(["cities"=>City::orderBy("name")->get()]);
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
    public function store(CityRequest $request)
    {
        $user=Auth::guard('api')->user();
        $response = Gate::forUser($user)->inspect('edit-category');

        if (! $response->allowed()){
            echo $response->message();
        }
        else{
            $city=City::create([
                "name"=>$request->name
            ]);
            if($city){
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
        return response()->json(["city"=>City::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CityRequest $request, $id)
    {
        $user=Auth::guard('api')->user();
        $response = Gate::forUser($user)->inspect('edit-category');
        
        if (! $response->allowed()){
            echo $response->message();
        }

        else{
            $city=City::find($id);
            $success=$city->update([
                "name"=>$request->name
            ]);
         
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

        $user=Auth::guard('api')->user();
        $response = Gate::forUser($user)->inspect('edit-category');
        if (! $response->allowed()){
            echo $response->message();
        }
        else{
            $city=City::find($id);
            if($city->delete()){ 
                return response()->json(["success"=>true]);
            }
            else{
                return response()->json(["success"=>false]);
            }
        }

       
    }
}
