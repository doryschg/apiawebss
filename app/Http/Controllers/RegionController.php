<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use awebss\Models\Region;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RegionController extends Controller
{

      public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }


     public function index()
    {
         $regiones = Region::all();

    return response()->json(['status'=>'ok',"msg"=>"exito",'region'=>$regiones], 200);
    }

    public function store(Request $request)
    {
        
        $regiones = new Region();
        $regiones->reg_nombre = $request->reg_nombre;
        $regiones->save();
         return response()->json([
                "msg" => "exito",
                "reg_id" => $regiones->reg_id
            ], 200
        );
    }
    public function show($reg_id)
    {
    	$regiones = Region::find($reg_id);

         if (!$regiones)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una region con ese código.'])],404);
        }

    	return response()->json([
    		"msg" => "exito",
    		"regiones"=>$regiones
    		], 200
    	);
    }

     public function update(Request $request, $reg_id)
    {
    	$regiones= Departamento::find($reg_id);

         if (!$regiones)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una region con ese código.'])],404);
        }
    	
    	$regiones->reg_nombre = $request->reg_nombre;
        $regiones->save();
        return response()->json([
        		"msg" => "succes"
        	], 200
        );
    }

     public function destroy($reg_id)
    {
    	$regiones = Region::find($reg_id);

         if (!$regiones)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una region con ese código.'])],404);
        }

    	$regiones->delete();

    	return response()->json([
    		"msg" => "exito"
    		], 200
    	);
    }
}
