<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use JWTAuth;
use awebss\User;
use Validator;
use Illuminate\Support\Str;
use awebss\Models\Persona;
use awebss\Models\Imagen;
use awebss\Models\Direccion;

class PersonaController extends Controller
{   
    public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','pasar_persona_temporal']]);
    }  
public function index()
    {
    $personas = Persona::orderBy('per_nombres')->get();

return response()->json(['persona'=>$personas], 200);
    }

public function store(Request $request)
    {    
        $personas = new Persona();
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
        $personas->per_clave_publica= $request->per_clave_publica; 
        $personas->per_tipo_documento= $request->per_tipo_documento;
        $personas->per_pais= $request->per_pais;
        $personas->save();  

        //creando la imagen de la persona
        $imagen = new Imagen();
        $imagen->per_id=$personas->per_id;
        $imagen->ima_nombre=$request->ima_nombre;
        $imagen->ima_enlace=$request->ima_enlace;
        $imagen->ima_tipo=$request->ima_tipo;
        $imagen->save();

        // creando la direccion del a persona
        $direcciones = new Direccion();
        $direcciones->per_id=$personas->per_id;
        $direcciones->mun_id=$request->mun_id;
        $direcciones->dir_zona_comunidad= Str::upper($request->dir_zona_comunidad);
        $direcciones->dir_avenida_calle= Str::upper($request->dir_avenida_calle);
        $direcciones->dir_numero=$request->dir_numero;
        $direcciones->dir_tipo=$request->dir_tipo;
        $direcciones->save();

        $resultado=compact('personas','imagen','direcciones');

         return response()->json([
                "msg" => "exito",
                "persona" => $resultado
            ], 200
        );    
     }
public function buscar($per_ci)
    
    {

$personas=Persona::where('per_ci',$per_ci)->get();
$count= count($personas);

if($count>0)
{
    $c=1;

    $resultado=compact('c','personas');
    return response()->json($resultado,200);

}

$personas=\awebss\Models\Persona2::where('per_ci',$per_ci)->get();

$count= count($personas);

if($count>0)
{   
    $c=0;
    $resultado=compact('c','personas');
    return response()->json($resultado,200);
}

return response()->json([
                "msg" => "exito",
                "personas" => $personas
            ],200);
}


public function show($per_id)
    {

    $persona=Persona::find($per_id);

    if (!$persona)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra la persona con ese código.'])],404);
        }
    $imagen = Imagen::where('per_id', $per_id)->get();
    $direccion = Direccion::where('per_id', $per_id)->get();

    $result = compact('persona','imagen','direccion');

    return response()->json(['mensaje'=>'exito','persona'=>$result],200); 

    }
  
    public function update(Request $request, $per_id)
    {
        
        $personas = Persona::find($per_id);

         if (!$personas)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra la persona con ese código.'])],404);
        }

        $personas->per_ci = $request->per_ci;
        $personas->per_ci_expedido = $request->per_ci_expedido;
        $personas->per_nombres= Str::upper($request->per_nombres);
        $personas->per_apellido_primero= Str::upper($request->per_apellido_primero);
        $personas->per_apellido_segundo= Str::upper($request->per_apellido_segundo);
        $personas->per_fecha_nacimiento= $request->per_fecha_nacimiento;
        $personas->per_genero= $request->per_genero;
        $personas->per_email= $request->per_email;
        $personas->per_tipo_permanencia= $request->per_tipo_permanencia;
        $personas->per_tipo_documento= $request->per_tipo_documento;
        $personas->per_numero_celular= $request->per_numero_celular;
        $personas->per_clave_publica= $request->per_clave_publica;
        $personas->per_pais= $request->per_pais;
        $personas->save();

        $imagenes = Imagen::where('per_id', $per_id)->get()->first();
        $ima_id=$imagenes->ima_id;

        // editando los campos de la imagen

        $imagen = Imagen::find($ima_id);
        $imagen->ima_nombre=$request->ima_nombre;
        $imagen->ima_enlace=$request->ima_enlace;
        $imagen->ima_tipo=$request->ima_tipo;
        $imagen->save();

        $direcciones = Direccion::where('per_id', $per_id)->get()->first();
        $dir_id=$direcciones->dir_id;

        // editando los campos de direcciones

        $direcciones = Direccion::find($dir_id);
        $direcciones->mun_id=$request->mun_id;
        $direcciones->dir_zona_comunidad= Str::upper($request->dir_zona_comunidad);
        $direcciones->dir_avenida_calle= Str::upper($request->dir_avenida_calle);
        $direcciones->dir_numero=$request->dir_numero;
        $direcciones->dir_tipo=$request->dir_tipo;
        $direcciones->save(); 

        $resultado=compact('personas','imagen','direcciones');

         return response()->json([
            "msg" => "exito",
            "persona" => $resultado
            ], 200
        );
    }

// permite la creacion de una persona que esta en la tabla temporal a la tabla persona

 public function pasar_persona_temporal(Request $request)
    {
        $per_id_2=$request->per_id;
        $persona_temporal=\awebss\Models\Persona2::find($per_id_2);
        $persona_temporal->per_valida='TRUE';
        $persona_temporal->save();

        $personas = new Persona();
        $personas->per_ci=$request->per_ci;
        $personas->per_ci_expedido = $request->per_ci_expedido;
        $personas->per_nombres= Str::upper($request->per_nombres);
        $personas->per_apellido_primero= Str::upper($request->per_apellido_primero);
        $personas->per_apellido_segundo= Str::upper($request->per_apellido_segundo);
        $personas->per_fecha_nacimiento= $request->per_fecha_nacimiento;
        $personas->per_genero= $request->per_genero;
        $personas->per_email= $request->per_email;
        $personas->per_tipo_permanencia= $request->per_tipo_permanencia;
        $personas->per_numero_celular= $request->per_numero_celular;
        $personas->per_clave_publica= $request->per_clave_publica; 
        $personas->per_tipo_documento= $request->per_tipo_documento;
        $personas->per_pais= $request->per_pais;
        $personas->userid_at=JWTAuth::toUser()->id;
        $personas->save();  

        //creando la imagen de la persona

        $imagen = new Imagen();
        $imagen->per_id=$personas->per_id;
        $imagen->ima_nombre=$request->ima_nombre;
        $imagen->ima_enlace=$request->ima_enlace;
        $imagen->ima_tipo=$request->ima_tipo;
        $imagen->userid_at=JWTAuth::toUser()->id;
         $imagen->save();

        // creando la direccion del a persona
        $direcciones = new Direccion();
        $direcciones->per_id=$personas->per_id;
        $direcciones->mun_id=$request->mun_id;
        $direcciones->dir_zona_comunidad= Str::upper($request->dir_zona_comunidad);
        $direcciones->dir_avenida_calle= Str::upper($request->dir_avenida_calle);
        $direcciones->dir_numero=$request->dir_numero;
        $direcciones->dir_tipo=$request->dir_tipo;
        $direcciones->userid_at=JWTAuth::toUser()->id;
        $direcciones->save();

        $resultado=compact('personas','imagen','direcciones');

         return response()->json([
                "msg" => "exito",
                "persona" => $resultado
            ], 200
        ); 

    }

public function habilitar_cuentas($per_ci)
    
    {

$personas=\awebss\Models\Persona::where('per_ci',$per_ci)->first();

$count= count($personas);

if($count==0)
{
    $estado=-1;

    $resultado=compact('estado');
    return response()->json([
                'status'=>'ok',"msg" => "exito",
                "persona" => $resultado
            ], 200);
}
$per_id=$personas->per_id;

$pacientes=\awebss\Models\Paciente::where('per_id',$per_id)->get();

$count= count($pacientes);

if($count>0)
{
    
        $per_fecha_nacimiento=$personas->per_fecha_nacimiento;
        $edad=Persona::edad($per_fecha_nacimiento);
  
      if($edad>=15)
        {
        $estado=0;

        $resultado=compact('estado','personas','pacientes'); 

    return response()->json([
                'status'=>'ok',"msg" => "exito",
                "persona" => $resultado
            ], 200);
        }  
        else
        {
            $estado=1;
            $resultado=compact('estado');

            return response()->json([
                'status'=>'ok',"msg" => "exito",
                "persona" => $resultado
            ], 200);
        }
}

$estado=-1;

$resultado=compact('estado','personas'); 

 return response()->json([
                'status'=>'ok',"msg" => "exito",
                "persona" => $resultado
            ], 200);
}

}
