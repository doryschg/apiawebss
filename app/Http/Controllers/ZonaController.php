<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;

class ZonaController extends Controller
{

    public function index()
    {
        $zona=\awebss\Models\Zona::all();

        return response()->json(['status'=>'ok','mensaje'=>'exito','zona'=>$zona],200);
    }

    public function show($mun_id)
    {   
        $zona=\awebss\Models\Zona::where('mun_id',$mun_id)->orderBy('zon_nombre')->get();

         if (!$zona)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra zonas en ese municipio ese código.'])],404);
        }

        return response()->json(['status'=>'ok','mensaje'=>'exito','zona'=>$zona],200);
        
    }

// permite listar los establecimientos de salud dado una zona

     public function establecimientos_por_zona($zon_id)
    {   
        $cobertura=\awebss\Models\Cobertura::where('zon_id',$zon_id)->join('establecimiento_salud','establecimiento_salud.es_id','=','cobertura.es_id')->join('institucion','institucion.ins_id','=','establecimiento_salud.ins_id')->join('subsector','subsector.ss_id','=','institucion.ss_id')->where('establecimiento_salud.es_nivel','PRIMER NIVEL')->where('subsector.ss_nombre','PUBLICO')->get(['establecimiento_salud.es_id','es_nombre']);

         if (!$cobertura)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra establecimeintos de salud asociados a esta zona.'])],404);
        }

        return response()->json(['status'=>'ok','mensaje'=>'exito','zona'=>$cobertura],200);
        }


    public function pre_registro_establecimiento($es_id)
    {   
        
        $cobertura=\awebss\Models\Cobertura::where('es_id',$es_id)->join('establecimiento_salud','establecimiento_salud.es_id','=','cobertura.es_id')->join('Persona2','institucion.ins_id','=','establecimiento_salud.ins_id')->join('subsector','subsector.ss_id','=','institucion.ss_id')->where('establecimiento_salud.es_nivel','PRIMER NIVEL')->where('subsector.ss_nombre','PUBLICO')->get(['establecimiento_salud.es_id','es_nombre']);

         if (!$cobertura)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra establecimeintos de salud asociados a esta zona.'])],404);
        }

        return response()->json(['status'=>'ok','mensaje'=>'exito','zona'=>$cobertura],200);
        
    }

}
