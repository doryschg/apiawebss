<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;

use Validator;

use Illuminate\Support\Str;

class Persona2Controller extends Controller
{
  
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'mun_id' => 'required',
            'per_ci' => 'required', ]);

        if ($validator->fails()) 
        {
            return $validator->errors()->all();
        }   
        
        $personas = new \awebss\Models\Persona2();
        $personas->per_ci = $request->per_ci;
        $personas->per_ci_expedido = $request->per_ci_expedido;
        $personas->per_nombres= Str::upper($request->per_nombres);
        $personas->per_apellido_primero= Str::upper($request->per_apellido_primero);
        $personas->per_apellido_segundo= Str::upper($request->per_apellido_segundo);
        $personas->per_fecha_nacimiento= $request->per_fecha_nacimiento;
        $personas->per_genero= $request->per_genero;
        $personas->per_email= $request->per_email;
        $personas->per_tipo_permanencia= $request->per_tipo_permanencia;
        $personas->per_numero_celular= $request->per_numero_celular;
        $personas->per_tipo_documento= $request->per_tipo_documento;
        $personas->per_nacion= $request->per_nacion;
        $personas->mun_id=$request->mun_id;
        $personas->per_zona_comunidad= Str::upper($request->per_zona_comunidad);
        $personas->per_avenida_calle= Str::upper($request->per_avenida_calle);
        $personas->per_numero=$request->per_numero;
        $personas->per_valida='FALSE';
        $personas->per_origen='WEB';
        $personas->save();
         return response()->json([
                "msg" => "exito",
                "persona" => $personas
            ], 200
        );    
    }

   
    public function show($per_id)
    {
        $persona=\awebss\Models\Persona2::find($per_id);

    if (!$persona)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra la persona temporal con ese código.'])],404);
        }

    return response()->json(['mensaje'=>'exito','persona'=>$persona],200); 
    }
}
