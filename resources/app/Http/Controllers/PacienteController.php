<?php

namespace awebss\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Illuminate\Support\Str;
use Carbon;
use Validator;
use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use awebss\Models\Paciente;
use awebss\Models\Persona;
use awebss\Models\Paciente_establecimiento;
use awebss\Models\Imagen;
use awebss\Models\Direccion;


class PacienteController extends Controller
{  

      public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','crear_persona_paciente']]);
    }
    
/**
 * @api {get} /pacientes Obtiene todos los pacientes registrados
 * @apiVersion 0.2.0
 * @apiName GetPaciente
 * @apiSuccess {Array} pacientes
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError pacientes[] array vacio.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "PacienteNotFound"
 *     }
 */
public function index()

{  
   $pacientes = Paciente::select('paciente.pac_id','pac_grupo_sanguineo','pac_alergia','persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','per_ci','per_fecha_nacimiento')->join('persona','persona.per_id','=','paciente.per_id')->orderBy('per_nombres')->get();

 return response()->json(['status'=>'ok',"msg" => "exito",'paciente'=>$pacientes],200); 
    }
/**
 * @api {get} /pacientes_edades Obtiene la edad de un paciente
 * @apiVersion 0.2.0
 * @apiName GetPacienteEdad
 * @apiParam {Number} pac_id ID del paciente
 * @apiSuccess {array} paciente_edad
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un paciente con ese código
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "EdadNotFound"
 *     }
 */ 

 public function calcular_edad($pac_id)
    { 
$paciente=Paciente::find($pac_id);
     
        if (!$paciente)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un paciente con ese código.'])],404);
        }

$per_id=$paciente->per_id;

$persona=Persona::find($per_id);

if (!$persona)
        {

return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una persona con ese código.'])],404);
        
        }

 $per_fecha_nacimiento=$persona->per_fecha_nacimiento;

$edad=Persona::calcular_edad($per_fecha_nacimiento);

 return response()->json(['status'=>'ok',"msg" => "exito",'paciente_edad'=>$edad],200); 

    }
/**
 * @api {post}/pacientes Crea información para un paciente.
 * @apiVersion 0.2.0
 * @apiName PostPaciente
 * @apiSuccess {Array} paciente
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 * @apiError BadResponse Los registros no han podido crearse
 *
 * @apiError BaD-Response:
 *     HTTP/1.1 400 Bad Response
 *     {
 *       "error": "BadResponse"
 *     }
 */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'seg_id' => 'required',
            'es_id' => 'required',
            'per_id' => 'required', ]);

        if ($validator->fails()) 
        {
            return $validator->errors()->all();
        } 

        $pacientes = new Paciente();
        $pacientes->seg_id=$request->seg_id;
        $pacientes->per_id=$request->per_id;
        $pacientes->pac_grupo_sanguineo = $request->pac_grupo_sanguineo;
        $pacientes->pac_alergia =Str::upper($request->pac_alergia);
        $pacientes->userid_at=JWTAuth::toUser()->id;
        $pacientes->save();

        $paciente_establecimiento= new Paciente_establecimiento();
        $paciente_establecimiento->es_id=$request->es_id;
        $paciente_establecimiento->pac_id=$pacientes->pac_id;
        $paciente_establecimiento->pe_hist_clinico=$request->pe_hist_clinico;
        $paciente_establecimiento->pe_estado='ACTIVO';
        $paciente_establecimiento->userid_at=JWTAuth::toUser()->id;
        $paciente_establecimiento->save();

        $per_id=$pacientes->per_id;
        $personas=Persona::find($per_id);

        $per_fecha_nacimiento=$personas->per_fecha_nacimiento;
        $edad=Persona::edad($per_fecha_nacimiento);
  
      if($edad>=15)
        {
$usuarios=\awebss\User::where('per_id',$per_id)->first();

$count= count($usuarios);

if($count<=0)
{
        $per_ci=$personas->per_ci;
        $per_fecha_nacimiento=$personas->per_fecha_nacimiento;
        $contrasena= \awebss\User::generar_contraseña($per_ci,$per_fecha_nacimiento);
        $usuarios= \awebss\User::crear_cuenta($contrasena,$per_ci,$per_id);   
        }

 $roles=\awebss\Models\Rol_usuario::crear_rol($usuarios->id,7);

       }

        $resultado=compact('pacientes','paciente_establecimiento','usuarios','roles');

        return response()->json([
            'status'=>'ok',
            "msg" => "exito",
            "paciente" => $resultado
            ], 200
        );  
    }
/**
 * @api {post}/pacientes_personas Crea información para un paciente y persona.
 * @apiVersion 0.2.0
 * @apiName PostPacientePersona
 * @apiSuccess {Array} paciente
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 * @apiError BadResponse Los registros no han podido crearse
 *
 * @apiError BaD-Response:
 *     HTTP/1.1 400 Bad Response
 *     {
 *       "error": "BadResponse"
 *     }
 */

 public function crear_persona_paciente(Request $request)
    
    {
        $validator = Validator::make($request->all(), [
            
            'seg_id' => 'required',
            'es_id' => 'required',
            'mun_id'=>'required',]);

        if ($validator->fails()) 
        {
            return $validator->errors()->all();
        }

        $personas = new Persona();
        
        $per_tipo_documento=$request->per_tipo_documento; 

        if($per_tipo_documento=='SIN DOCUMENTO')

        { 

        $per_fecha_nacimiento=$request->per_fecha_nacimiento;

        $per_fecha=Carbon::parse($per_fecha_nacimiento)->format('d-m-Y');

        $per_nombres= Str::upper($request->per_nombres);
        $per_apellido_primero= Str::upper($request->per_apellido_primero);
        $per_apellido_segundo= Str::upper($request->per_apellido_segundo);
       
        $valor1 = substr($per_apellido_primero,0,1);
        $valor2 = substr($per_apellido_segundo,0,1);
        $valor3 = substr($per_nombres,0,1);
        $personas->per_ci =$valor1.$valor2.$valor3.$per_fecha; }

        else {
        $personas->per_ci = $request->per_ci; 
        }

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
        $personas->per_tipo_documento= $per_tipo_documento;
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
        
        $pacientes = new Paciente();
        $pacientes->seg_id=$request->seg_id;
        $pacientes->per_id=$personas->per_id;
        $pacientes->pac_grupo_sanguineo = $request->pac_grupo_sanguineo;
        $pacientes->pac_alergia =Str::upper($request->pac_alergia);
        $pacientes->userid_at=JWTAuth::toUser()->id;
        $pacientes->save();

        $paciente_establecimiento= new Paciente_establecimiento();
        $paciente_establecimiento->es_id=$request->es_id;
        $paciente_establecimiento->pac_id=$pacientes->pac_id;
        $paciente_establecimiento->pe_hist_clinico=$request->pe_hist_clinico;
        $paciente_establecimiento->pe_estado='ACTIVO';
        $paciente_establecimiento->userid_at=JWTAuth::toUser()->id;
        $paciente_establecimiento->save();

        $per_fecha_nacimiento=$personas->per_fecha_nacimiento;

        $edad=Persona::edad($per_fecha_nacimiento);

      if($edad>=15)
        {

        $per_id=$pacientes->per_id;
        $personas=Persona::find($per_id);
        $per_ci=$personas->per_ci;
        $per_fecha_nacimiento=$personas->per_fecha_nacimiento;
        $contrasena= \awebss\User::generar_contraseña($per_ci,$per_fecha_nacimiento);
        $usuarios= \awebss\User::crear_cuenta($contrasena,$per_ci,$per_id);
        $roles=\awebss\Models\Rol_usuario::crear_rol($usuarios->id,7);
       
        } 

        $resultado=compact('personas','imagen','direcciones','pacientes','paciente_establecimiento','usuarios','roles');

       return response()->json(["msg" => "exito","paciente" => $resultado], 200);
}

/**
 * @api {get} /pacientes Obtiene la informacion de un paciente
 * @apiVersion 0.2.0
 * @apiName GetPaciente
 * @apiParam {Number} pac_id ID del paciente
 * @apiSuccess {array} paciente
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un paciente con ese código
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "EdadNotFound"
 *     }
 */ 
    public function show(Request $request, $pac_id)
    {

    $validator = Validator::make($request->all(), [

            'es_id' => 'required',
        ]);

    if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }

  $es_id=$request->es_id;

  $paciente= Paciente::find($pac_id);
   
        if (!$paciente)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un paciente con ese código.'])],404);
        }
$paciente= Paciente::find($pac_id);
$per_id=$paciente->per_id;
$persona=Persona::find($per_id);
$per_fecha_nacimiento=$persona->per_fecha_nacimiento;

$edad=Persona::calcular_edad($per_fecha_nacimiento);

$direccion=Direccion::where('per_id',$per_id)->get();

$paciente_establecimiento=Paciente_establecimiento::where('pac_id',$pac_id)->where('es_id',$es_id)->get();

$resultado=compact('persona','paciente','paciente_establecimiento','direccion','edad');

return response()->json(['status'=>'ok',"msg" => "exito",'paciente'=>$resultado],200); 

    }
/**
 * @api {put} /pacientes/:pac_id modifica los campos de paciente. 
 * @apiVersion 0.2.0
 * @apiName PutPaciente
 
 * @apiParam {Number} pac_id Paciente unique ID.
 * @apiSuccess {Array} paciente
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra un pacientecon ese código.'
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "PacienteNotFound"
 *     }
 */ 
    public function update(Request $request, $pac_id)
    {
       $pacientes= Paciente::find($pac_id);
     
        if (!$pacientes)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un paciente con ese código.'])],404);
        }
        $input=$request->all();

        $es_id=$request->es_id;
        $pacientes->seg_id=$request->seg_id;
        $pacientes->pac_grupo_sanguineo = $request->pac_grupo_sanguineo;
        $pacientes->pac_alergia =Str::upper($request->pac_alergia);
        $pacientes->save();

        $paciente_establecimiento= Paciente_establecimiento::where('pac_id',$pac_id)->get();

foreach ($paciente_establecimiento as $pacientees) 

{  $es_id_p=$pacientees->es_id;

if($es_id==$es_id_p)

   $pe_id=$pacientees->pe_id;

}
    $paciente_establecimiento=Paciente_establecimiento::find($pe_id);
    $paciente_establecimiento->update($input); 

    $resultado=compact('pacientes','paciente_establecimiento');

    return response()->json(['status'=>'ok',"msg" => "exito",'paciente'=>$resultado],200); 
        
    }
/**
 * @api {get} /pacientes_es Obtiene los pacientes de un establecimiento de salud
 * @apiVersion 0.2.0
 * @apiName GetPaciente
 * @apiParam {Number} pac_id ID del paciente
 * @apiSuccess {array} paciente
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError paciente[], array vacio
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "EdadNotFound"
 *     }
 */ 

    public function paciente_es(Request $request, $es_id)
    {

    $nro=$request->nro;
        
  $pacientes=Paciente::select('paciente.pac_id','paciente_establecimiento.es_id','pe_hist_clinico','persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','per_ci','per_fecha_nacimiento')->join('paciente_establecimiento','paciente_establecimiento.pac_id','=','paciente.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->where('paciente_establecimiento.es_id',$es_id)->where('paciente_establecimiento.pe_estado','like','ACTIVO')->orderBy('per_apellido_primero')->paginate($nro); 

        return response()->json(['status'=>'ok',"msg" => "exito",'paciente'=>$pacientes],200); 
    }

/**
 * @api {get} /pacientes_cedulas Obtiene al paciente de acuerdo a su cedula de identidad
 * @apiVersion 0.2.0
 * @apiName GetPacienteCi
 * @apiParam {Number} pac_id ID del paciente
 * @apiSuccess {array} persona_paciente
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError persona_paciente[], array vacio
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "EdadNotFound"
 *     }
 */ 

     public function buscar_paciente($per_ci)
    {

$persona_paciente=Persona::where('per_ci',$per_ci)->join('paciente','paciente.per_id','=','persona.per_id')->whereNull('paciente.deleted_at')->select('persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','per_ci','per_fecha_nacimiento','per_email','per_numero_celular','paciente.pac_id','pac_alergia','pac_grupo_sanguineo','paciente.seg_id')->get();

        return response()->json($persona_paciente,200); 
    }
/**
 * @api {get} /pacientes_ci Obtiene un paciente de acuerdo a su cedula de identidad, devuelve un objeto
 * @apiVersion 0.2.0
 * @apiName GetPaciente
 * @apiParam {Number} pac_id ID del paciente
 * @apiSuccess {array} paciente, paciente[], array vacio
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError 
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "PacienteNotFound"
 *     }
 */ 
    public function paciente_ci($per_ci)
    {

$persona_paciente=Persona::where('per_ci',$per_ci)->join('paciente','paciente.per_id','=','persona.per_id')->select('persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','per_ci','per_fecha_nacimiento','per_email','per_numero_celular','paciente.pac_id','pac_alergia','pac_grupo_sanguineo','paciente.seg_id')->get();
return response()->json(['status'=>'ok',"msg" => "exito",'paciente'=>$persona_paciente],200);  
    }

/**
 * @api {get} /pacientes_personas Obtiene un paciente de acuerdo al per_id
 * @apiVersion 0.2.0
 * @apiName GetPaciente
 * @apiParam {Number} per_id ID de la persona
 * @apiSuccess {array} paciente
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError paciente[], array vacio
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "EdadNotFound"
 *     }
 */ 

public function ver_paciente_per_id($per_id)
    {

$persona_paciente=Paciente::where('per_id',$per_id)->get();

 return response()->json(['status'=>'ok',"msg" => "exito",'paciente'=>$persona_paciente],200);  
    }

}
