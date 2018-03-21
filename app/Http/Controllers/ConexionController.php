<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;

use awebss\Models\Conexion;

class ConexionController extends Controller
{
    
 public function index()
    {

    $establecimiento= Conexion::join('establecimiento_salud','establecimiento_salud.es_id','=','conexion.es_id')->select('establecimiento_salud.es_id','es_nombre','conexion.con_id','con_ip','con_puerto','con_instancia','con_bd','con_usuario','con_password','con_inicio_vigencia','con_final_vigencia')->get();

     return response()->json(['status'=>'ok','mensaje'=>'exito','establecimiento'=>$establecimiento],200);     
    }
     public function store(Request $request)
    {

        $conexion= new Conexion();
        $conexion->con_ip=$request->con_ip;
        $conexion->con_puerto=$request->con_puerto;
        $conexion->con_instancia=$request->con_instancia;
        $conexion->con_bd=$request->con_bd;
        $conexion->con_usuario=$request->con_usuario;
        $conexion->con_password=$request->con_password;
        $conexion->con_inicio_vigencia=$request->con_inicio_vigencia;
        $conexion->con_final_vigencia=$request->con_final_vigencia;
        $conexion->es_id=$request->es_id;
        $conexion->save();

return response()->json([
                'status'=>'ok',"msg" => "exito",
          "conexion" => $conexion
            ], 200
        ); 
    }


    public function update(Request $request, $con_id)
    {

        $conexion= Conexion::find($con_id);

         if(!$conexion)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un control de crecimiento y signos vitales.'])],404);
        }

        $input=$request->all();
        $conexion->update($input);
     
return response()->json([
                'status'=>'ok',"msg" => "exito",
          "conexion" => $conexion
            ], 200
        ); 
    }

  public function show(Request $request, $es_id)
    {

         $conexion= Conexion::where('es_id',$es_id)->get();

        // if(count($conexion)<=0)
       // {
           // return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una conexion con ese codigo.'])],404);
        //}

        return response()->json(['status'=>'ok','mensaje'=>'exito','conexion'=>$conexion],200);

    }

}
