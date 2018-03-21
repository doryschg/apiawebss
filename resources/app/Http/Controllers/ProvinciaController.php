<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use awebss\Models\Provincia;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ProvinciaController extends Controller
{
    
      public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }
    
     public function index()
    {
        
        $provincias = Provincia::all();

    return response()->json(['status'=>'ok',"msg"=>"exito",'provincia'=>$provincias], 200);
    }

    public function store(Request $request)
    {
        $provincias = new Provincia();
        $provincias->dep_id = $request->dep_id;
        $provincias->pro_nombre = $request->pro_nombre;
        $provincias->save();

        return response()->json([
            "msg" => "exito",
            "pro_id" => $provincias->pro_id
            ], 200
        );
        
    }

    public function show($pro_id)
    {
        $provincias= Provincia::find($pro_id);

        if (!$provincias)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una provincia con ese código.'])],404);
        }

        return response()->json(['status'=>'ok','provincia'=>$provincias],200);
        
    }


    public function update(Request $request, $id)
    {
        $provincias = Provincia::find($pro_id);

         if (!$provincias)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una provincia con ese código.'])],404);
        }
        $provincias->dep_id = $request->dep_id;
        $provincias->pro_nombre = $request->pro_nombre;
        $provincias->save();

        return response()->json([
            "msg" => "exito"
            ],200
        );
    }

    public function destroy($id)
    {
        $provincias = Provincia::find($pro_id);

         if (!$provincias)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una provincia con ese código.'])],404);
        }


        $provincias->delete();

        return response()->json([
            "msg" => "exito"
            ], 200
        );
    }
}
