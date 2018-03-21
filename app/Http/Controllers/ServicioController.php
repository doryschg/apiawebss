<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;

class ServicioController extends Controller
{
    
    public function index()
    {

        $servicios=\awebss\Models\Servicio::all();
  
        return response()->json(["msg"=>"ok",'servicio'=>$servicios],200);
    }
  
  public function show($es_id)
    {

      
     $establecimiento= \awebss\Models\Establecimiento_salud::find($es_id);

    if (!$establecimiento)
    {
        return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un establecimiento con ese código.'])],404);
    }


$servicio_establecimiento=\awebss\Models\Servicio_establecimiento::where('es_id',$es_id)->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->where('servicio.ser_tipo','ESPECIALIDAD')->select('servicio_establecimiento.se_id','es_id','se_necesita_ref','servicio.ser_id','ser_nombre','ser_tipo')->orderBy('ser_tipo')->get();

 return response()->json(['status'=>'ok','servicio'=>$servicio_establecimiento],200); 
    }
}
