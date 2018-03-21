<?php

namespace awebss\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Illuminate\Database\Eloquent\Collection;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;
use Validator;
use Carbon;
use awebss\Models\Persona;
use awebss\Models\Imagen;
use awebss\Models\Direccion;
use awebss\Models\Funcionario;
use awebss\Models\Funcionario_establecimiento;
use awebss\Models\Medico;
use awebss\Models\Enfermera;
use awebss\Models\Configuracion_horario;

class FuncionarioController extends Controller
{
  public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','crear_funcionario']]);
    }
/**
 * @api {get} /funcionarios Obtiene todos los funcionarios resgistrados
 * @apiVersion 0.2.0
 * @apiName GetFuncionario
 * @apiSuccess {Array} funcionario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra funcionarios en ese establecimiento.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ConsultorioNotFound"
 *     }
 */

      public function index()
  {

$funcionario = Funcionario::select('persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','per_ci','funcionario.fun_id','funcionario_establecimiento.fe_id','fe_memorandum','fe_inicio_trabajo','fe_fin_trabajo','fe_cargo','fe_estado_laboral','fe_carga_laboral','funcionario_establecimiento.es_id')
->join('persona','persona.per_id','=','funcionario.per_id')->join('funcionario_establecimiento','funcionario_establecimiento.fun_id','=','funcionario.fun_id')->orderBy('per_nombres')->get(); 
$count=count($funcionario);

 if($count<=0)
    {

return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentran funcionarios.'])],404);

    }    

 return response()->json(['status'=>'ok',"msg" => "exito",'funcionario'=>$funcionario],200); 
    }
/**
 * @api {get} /funcionarioes Obtiene los funcionarios de un establecimiento de salud
 * @apiVersion 0.2.0
 * @apiName GetFuncionarioEstablecimiento
 * @apiParam {Number} es_id ID del establecimiento
 * @apiSuccess {array} funcionarios
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError funcionarios[].
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "FuncionarioNotFound"
 *     }
 */ 
     public function listar_funcionario($es_id)
    {

$funcionarios=Funcionario::select('funcionario.fun_id','fun_profesion','funcionario_establecimiento.fe_id','fe_memorandum','fe_inicio_trabajo','fe_fin_trabajo','fe_cargo','fe_estado_laboral','fe_carga_laboral','es_id','persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','per_ci','per_fecha_nacimiento')->join('funcionario_establecimiento','funcionario_establecimiento.fun_id','=','funcionario.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->where('funcionario_establecimiento.es_id',$es_id)->where('funcionario_establecimiento.fe_estado','ACTIVO')->orderBy('per_nombres')->get(); 


return response()->json(['status'=>'ok',"msg" => "exito",'funcionario'=>$funcionarios],200); 
    }
/**
 * @api {post}/funcionarios_persona Crea información para un funcionario y persona.
 * @apiVersion 0.2.0
 * @apiName PostFunccionario
 * @apiSuccess {Array} funcionario
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
public function crear_funcionario(Request $request, $es_id)
    { 
        //creando a la persona 
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
    
        // creando la direccion del la persona

        $direcciones = new Direccion();
        $direcciones->per_id=$personas->per_id;
        $direcciones->mun_id=$request->mun_id;
        $direcciones->dir_zona_comunidad= Str::upper($request->dir_zona_comunidad);
        $direcciones->dir_avenida_calle= Str::upper($request->dir_avenida_calle);
        $direcciones->dir_numero= $request->dir_numero;
        $direcciones->dir_tipo=$request->dir_tipo;   
        $direcciones->userid_at=JWTAuth::toUser()->id;
        $direcciones->save();
        
        //creando al funcionario

        $funcionarios= new Funcionario();
        $funcionarios->per_id=$personas->per_id;
        $funcionarios->fun_profesion=$request->fun_profesion;
        $funcionarios->userid_at=JWTAuth::toUser()->id;
        $funcionarios->save();

        // creando al funcionario establecimiento

        $funcionario_establecimiento= new Funcionario_establecimiento();
        $funcionario_establecimiento->es_id=$es_id;
        $funcionario_establecimiento->fun_id=$funcionarios->fun_id;
        $funcionario_establecimiento->fe_memorandum = $request->fe_memorandum;
        $funcionario_establecimiento->fe_inicio_trabajo = $request->fe_inicio_trabajo;
        $funcionario_establecimiento->fe_fin_trabajo = $request->fe_fin_trabajo;
        $funcionario_establecimiento->fe_estado_laboral = Str::upper($request->fe_estado_laboral);
        $funcionario_establecimiento->fe_cargo =Str::upper($request->fe_cargo);
        $funcionario_establecimiento->fe_estado='ACTIVO';
        $funcionario_establecimiento->userid_at=JWTAuth::toUser()->id;
        $funcionario_establecimiento->save();

        if($funcionarios->fun_profesion=='MEDICO')
        {
            $medico= new Medico();
            $medico->fun_id=$funcionarios->fun_id;
            $medico->userid_at=JWTAuth::toUser()->id;
            $medico->save();
        }

        if($funcionarios->fun_profesion=='ENFERMERA')
        {
            $enfermera= new Enfermera();
            $enfermera->fun_id=$funcionarios->fun_id;
            $enfermera->userid_at=JWTAuth::toUser()->id;
            $enfermera->save();
        }
    $resultado=compact('personas','imagen','direcciones','funcionarios','funcionario_establecimiento','medico','enfermera');

         return response()->json([
                'status'=>'ok',"msg" => "exito",
                "funcionario" => $resultado
            ], 200
        );
    }
/**
 * @api {post}/funcionarios Crea información para un funcionario
 * @apiVersion 0.2.0
 * @apiName PostFunccionario
 * @apiSuccess {Array} funcionario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
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
            
            'per_id' => 'required',     
        ]);

        if ($validator->fails()) 

        {
            return $validator->errors()->all();
        } 
    $fun_profesion=$request->fun_profesion;   

        // crear al funcionario si existe la persona

    if($fun_profesion!=null)
    {
		$funcionarios= new Funcionario();
        $funcionarios->per_id=$request->per_id;
        $funcionarios->fun_profesion=$request->fun_profesion;
        $funcionarios->userid_at=JWTAuth::toUser()->id;
        $funcionarios->save();

         if($funcionarios->fun_profesion=='MEDICO')
        {
            $medico= new Medico();
            $medico->fun_id=$funcionarios->fun_id;
            $medico->userid_at=JWTAuth::toUser()->id;
            $medico->save();
        }

        if($funcionarios->fun_profesion=='ENFERMERA')
        {
            $enfermera= new Enfermera();
            $enfermera->fun_id=$funcionarios->fun_id;
            $enfermera->userid_at=JWTAuth::toUser()->id;
            $enfermera->save();
        }

     // creando al funcionario establecimiento

        $funcionario_establecimiento= new Funcionario_establecimiento();
        $funcionario_establecimiento->es_id=$request->es_id;
        $funcionario_establecimiento->fun_id=$funcionarios->fun_id;
        $funcionario_establecimiento->fe_memorandum = $request->fe_memorandum;
        $funcionario_establecimiento->fe_inicio_trabajo = $request->fe_inicio_trabajo;
        $funcionario_establecimiento->fe_fin_trabajo = $request->fe_fin_trabajo;
        $funcionario_establecimiento->fe_estado_laboral = Str::upper($request->fe_estado_laboral);
        $funcionario_establecimiento->fe_cargo =Str::upper($request->fe_cargo);
        $funcionario_establecimiento->fe_estado='ACTIVO';
        $funcionario_establecimiento->userid_at=JWTAuth::toUser()->id;
        $funcionario_establecimiento->save();

    }

    else {

     // creando al funcionario establecimiento

        $funcionario_establecimiento= new Funcionario_establecimiento();
        $funcionario_establecimiento->es_id=$request->es_id;
        $funcionario_establecimiento->fun_id=$request->fun_id;
        $funcionario_establecimiento->fe_memorandum = $request->fe_memorandum;
        $funcionario_establecimiento->fe_inicio_trabajo = $request->fe_inicio_trabajo;
        $funcionario_establecimiento->fe_fin_trabajo = $request->fe_fin_trabajo;
        $funcionario_establecimiento->fe_estado_laboral = Str::upper($request->fe_estado_laboral);
        $funcionario_establecimiento->fe_cargo =Str::upper($request->fe_cargo);
        $funcionario_establecimiento->fe_estado='ACTIVO';
        $funcionario_establecimiento->userid_at=JWTAuth::toUser()->id;
        $funcionario_establecimiento->save();

    }

         $resultado=compact('funcionarios','funcionario_establecimiento','medico','enfermera');

        return response()->json([
            'status'=>'ok',"msg" => "exito",
            "funcionario" => $resultado
            ], 200);    
    }
/**
 * @api {get} /funcionarios Obtiene informacion de un funcionario
 * @apiVersion 0.2.0
 * @apiName GetFuncionario
 * @apiParam {Number} fe_id ID del funcionario
 * @apiSuccess {array} funcionario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un funcionario con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "FuncionarioNotFound"
 *     }
 */ 
 public function show(Request $request, $fe_id)
    {

    $funcionario_establecimiento=Funcionario_establecimiento::find($fe_id);

    if (!$funcionario_establecimiento)
        {
    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un funcionario con ese código.'])],404);
        }

    $fun_id=$funcionario_establecimiento->fun_id;

    $funcionario= Funcionario::find($fun_id);

    $per_id=$funcionario->per_id;

    $persona=Persona::find($per_id);
    $direccion=Direccion::where('per_id',$per_id)->get();

    $resultado=compact('persona','direccion','funcionario','funcionario_establecimiento');

    return response()->json(['status'=>'ok',"msg" => "exito",'funcionario'=>$resultado],200); 
    }
/**
 * @api {get} /funcionarios_per Obtiene informacion de un funcionario dado el per_id
 * @apiVersion 0.2.0
 * @apiName GetPersona
 * @apiParam {Number} per_id ID de la persona
 * @apiSuccess {array} funcionario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra una persona con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "FuncionarioNotFound"
 *     }
 */
     public function ver_funcionario($per_id)
    {

    $funcionario=Funcionario::where('per_id',$per_id)->first();

    if (!$funcionario)
        {
    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una persona con ese código.'])],404);
        }

  $fun_id=$funcionario->fun_id;


  $funcionario_establecimiento=Funcionario_establecimiento::where('fun_id',$fun_id)->join('establecimiento_salud','establecimiento_salud.es_id','=','funcionario_establecimiento.es_id')->select('funcionario_establecimiento.fe_id','fe_estado','establecimiento_salud.es_id','es_nombre')->get();

  $resultado=compact('funcionario','funcionario_establecimiento');

    return response()->json(['status'=>'ok',"msg" => "exito",'funcionario'=>$resultado],200); 
    }

/**
 * @api {put} /funcionarios/:fe_id modifica los campos de funcionario. 
 * @apiVersion 0.2.0
 * @apiName PutFuncionario
 
 * @apiParam {Number} fe_id Funcionario unique ID.
 * @apiSuccess {Array} cita
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra un funcionario con ese código.'
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "FuncionarioNotFound"
 *     }
 */ 
    public function update(Request $request, $fe_id)

    {   
    $funcionario_establecimiento= Funcionario_establecimiento::find($fe_id);

    if (!$funcionario_establecimiento)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un funcionario con ese código.'])],404);
        }

        $input = $request->all();
        $funcionario_establecimiento->update($input);

return response()->json(['status'=>'ok',"msg" => "exito","funcionario" => $funcionario_establecimiento], 200);
    }
/**
 * @api {get} /funcionarios_per Obtiene los funcionarios que no tienen cuenta y que estan activos en el establecimeinto de salud
 * @apiVersion 0.2.0
 * @apiName GetPersona
 * @apiParam {Number} per_id ID de la persona
 * @apiSuccess {Array} funcionario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError funcionario[], array vacio
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "FuncionarioNotFound"
 *     }
 */ 
    public function listar_funcionario_sincuenta($es_id)
    {

$usuarios=Funcionario_establecimiento::where('es_id',$es_id)->join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->join('_usuario','_usuario.per_id','=','funcionario.per_id')->join('_rol_usuario','_rol_usuario.usu_id','=','_usuario.id')->where('funcionario_establecimiento.fe_estado','ACTIVO')->whereNotIn('_rol_usuario.rol_id',[7])->whereNotIn('_rol_usuario.rol_id',[1])->select('funcionario_establecimiento.fe_id')->get();

 $funcionarios=Funcionario_establecimiento::where('es_id',$es_id)->select('persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','funcionario_establecimiento.fe_id','fe_cargo')->join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->where('funcionario_establecimiento.fe_estado','ACTIVO')->join('persona','persona.per_id','=','funcionario.per_id')->whereNotIn('funcionario_establecimiento.fe_id',$usuarios)->get(); 

return response()->json(['status'=>'ok',"msg"=>"exito",'funcionario'=>$funcionarios],200);  
    }
/**
 * @api {get} /funcionarios_establecimientos Verifica que un funcionario pertenece a un establecimiento
 * @apiVersion 0.2.0
 * @apiName GetFuncionarios
 * @apiParam {Number} fe_id, es_id fe_id ID del funcionario, es_id ID del establecimiento
 * @apiSuccess {Number} True o Fales dependiendo del resultado
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError estado False.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "FuncionarioNotFound"
 *     }
 */ 
    public function buscar_funcionario($fe_id, $es_id)
    {   
        $funcionario=Funcionario_establecimiento::where('fe_id',$fe_id)->get();

        $estado='false';
        
        foreach($funcionario as $funcionario) {  
        $es_id_f=$funcionario->es_id;
        if($es_id_f==$es_id)
        {
            $estado='true'; } }
        
        return response()->json(['status'=>'ok',"msg" => "exito",'estado'=>$estado],200); 
    }

/**
 * @api {get} /funcionarios_horarios Obtiene los horarios de un funcionario
 * @apiVersion 0.2.0
 * @apiName GetFuncionarios
 * @apiParam {Number} fe_id, es_id fe_id ID del funcionario, es_id ID del establecimiento
 * @apiSuccess {Array} funcionario_horario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError funcionario[] array vacio.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "FuncionarioNotFound"
 *     }
 */ 
public function listar_horario_funcionario(Request $request, $fe_id)
  {

    $funcionario_horario=Configuracion_horario::where('fe_id',$fe_id)->join('servicio_consultorio','servicio_consultorio.sc_id','=','configuracion_horario.sc_id')->join('consultorio','consultorio.con_id','=','servicio_consultorio.con_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->select('configuracion_horario.ch_id','ch_fecha_inicio','ch_fecha_final','consultorio.con_id','con_nombre','servicio.ser_id','ser_nombre')->get();

         
        return response()->json(['status'=>'ok',"msg" => "exito",'funcionario'=>$funcionario_horario],200); 
    }

}
