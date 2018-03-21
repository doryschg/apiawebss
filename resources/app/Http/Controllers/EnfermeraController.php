<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;

use Validator;

class EnfermeraController extends Controller
{
    
    public function index()
    {
        $enfermera =\awebss\Models\Enfermera::select('persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','funcionario_establecimiento.fe_id','funcionario.fun_id','fun_profesion','fe_cargo','es_id')->join('funcionario','funcionario.fun_id','=','enfermera.fun_id')->join('funcionario_establecimiento','funcionario_establecimiento.fun_id','=','funcionario.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->get();

        return response()->json(['status'=>'ok','enfermera'=>$enfermera],200);
    }
   
    public function show($es_id)
    {
        
       $enfermeras=\awebss\Models\Enfermera::select('persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','funcionario_establecimiento.fe_id','funcionario.fun_id','fun_profesion','fe_cargo','es_id')->join('funcionario','funcionario.fun_id','=','enfermera.fun_id')->join('funcionario_establecimiento','funcionario_establecimiento.fun_id','=','funcionario.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->where('funcionario_establecimiento.es_id',$es_id)->->where('funcionario_establecimiento.fe_estado','ACTIVO')get();

        return response()->json(['status'=>'ok','mensaje'=>'exito','enfermera'=>$enfermeras],200); 
    }

}
