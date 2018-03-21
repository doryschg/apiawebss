<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use JWTAuth;
use Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;

class InstitucionController extends Controller
{

     public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }

    
    public function index()
    {
        $institucion=\awebss\Models\Institucion::all();

        return response()->json(['status'=>'ok','institucion'=>$institucion],200); 
    }

    public function store(Request $request)
    {
        $institucion= new \awebss\Models\Institucion();
        $institucion->ss_id=$request->ss_id;
        $institucion->ins_nombre=$request->ins_nombre;
        $institucion->ins_cod_sice=$request->ins_cod_sice;

        $institucion->save();

    return response()->json(['status'=>'ok','institucion'=>$institucion],200);
    }

    public function show($ins_id)
    {
         $institucion= \awebss\Models\Institucion::find($ins_id);
          if (!$institucion)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una institucion con ese código.'])],404);
        }

         return response()->json(['status'=>'ok','institucion'=>$institucion],200);
    }

    public function update(Request $request, $ins_id)
    {
        $institucion= \awebss\Models\Institucion::find($ins_id);
          if (!$institucion)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una institucion con ese código.'])],404);
        }
        $institucion->ins_nombre=$request->ins_nombre;
        $institucion->ins_cod_sice=$request->ins_cod_sice;
        $institucion->save();

    return response()->json(['status'=>'ok','institucion'=>$institucion],200);
    }

    public function destroy($ins_id)
    {
        
        $institucion= \awebss\Models\Institucion::find($ins_id);
          if (!$institucion)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una institucion con ese código.'])],404);
        }
        $institucion->delete();

         return response()->json(["msg"=>"registros eliminados correctamente"],200);
    }
}
