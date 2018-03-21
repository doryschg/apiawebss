<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class SeguroController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    } 
    
    public function index()
    {
        $seguro=\awebss\Models\Seguro::all();
        
        return response()->json(['status'=>'ok','mensaje'=>'exito','seguro'=>$seguro],200);
    }

    public function show($seg_id)
    {
        $seguro=\awebss\Models\Seguro::find($seg_id);

         if (!$seguro)
        {
    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un seguro con ese código.'])],404);
        }

        return response()->json(['status'=>'ok','mensaje'=>'exito','seguro'=>$seguro],200);
    }


     public function store(Request $request)
    {
        $seguros= new \awebss\Models\Seguro();
        $seguros->seg_nombre=$request->seg_nombre;
        $seguros->seg_descripcion=$request->seg_descripcion;
        $seguros->save();

    return response()->json(['status'=>'ok','seguro'=>$seguros],200);
        
    }

    public function update(Request $request, $seg_id)
    {
        
        $seguros= \awebss\Models\Seguro::find($seg_id);

          if (!$seguros)
        {
    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un seguro con ese código.'])],404);
        }

        $seguros->seg_nombre=$request->seg_nombre;
        $seguros->seg_descripcion=$request->seg_descripcion;
        $seguros->save();

    return response()->json(['status'=>'ok','seguro'=>$seguros],200);
    }
    

    public function destroy($seg_id)
    {

        $seguros= \awebss\Models\Seguro::find($seg_id);

          if (!$seguros)
        {
    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un seguro con ese código.'])],404);
        }
        $seguros->delete();
         return response()->json(["msg"=>"registros eliminados correctamente"],200
            );
        
    }
}
