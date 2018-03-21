<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;

class MedicoController extends Controller
{
    
    
    public function index()
    {
        $medico=\awebss\Models\Medico::select('persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','funcionario_establecimiento.fe_id','funcionario.fun_id','fun_profesion','fe_cargo','es_id')->join('funcionario','funcionario.fun_id','=','medico.fun_id')->join('funcionario_establecimiento','funcionario_establecimiento.fun_id','=','funcionario.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->get();

         return response()->json([
                'status'=>'ok',"msg" => "exito",
                "medico" => $medico
            ], 200
        );
    }

    public function show($es_id)
    {
        $medicos=\awebss\Models\Medico::select('persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','funcionario_establecimiento.fe_id','funcionario.fun_id','fun_profesion','fe_cargo','es_id')->join('funcionario','funcionario.fun_id','=','medico.fun_id')->join('funcionario_establecimiento','funcionario_establecimiento.fun_id','=','funcionario.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->where('funcionario_establecimiento.es_id',$es_id)->where('funcionario_establecimiento.fe_estado','ACTIVO')->get();

        return response()->json(['status'=>'ok','mensaje'=>'exito','medico'=>$medicos],200); 

    }
}
