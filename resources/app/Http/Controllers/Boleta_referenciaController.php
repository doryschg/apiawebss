<?php

namespace awebss\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use awebss\Models\Persona;
use awebss\Models\Paciente_establecimiento;
use awebss\Models\Establecimiento_salud;
use awebss\Models\Paciente;
use awebss\Models\Boleta_referencia;
use awebss\Models\Boleta_contrareferencia;
use awebss\Models\Funcionario_establecimiento;
use awebss\Models\Red;
use awebss\Models\Direccion;

class Boleta_referenciaController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['update','store','destroy','editar']]));
    }
/**
 * @api {get} /referencia Obtiene todas las boletas de referencia
 * @apiVersion 0.2.0
 * @apiName GetReferencia
 * @apiSuccess {array} referencia[]
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ReferenciaNotFound"
 *     }
 */ 
    public function index()
    {
      $referencia=Boleta_referencia::all();

          return response()->json(['status'=>'ok','referencia'=>$referencia],200);
    }
/**
 * @api {get} /referencias_establecimientos_origen Obtiene todas las boletas de referencia de un establecimiento origen
 * @apiVersion 0.2.0
 * @apiName GetReferenciaOrigen
 * @apiParam {Number} es_id ID del establecimiento de salud
 * @apiSuccess {array} referencia[]
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
  * @apiError No se encuentra un establecimiento con ese codigo
 */ 
public function lista_referencia($es_id_origen)
    {

      $establecimiento=Establecimiento_salud::find($es_id_origen);

          if (!$establecimiento)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un establecimiento con ese código.'])],404);
        }
$referencia = Boleta_referencia::where('es_id_origen',$es_id_origen)->join('establecimiento_salud','establecimiento_salud.es_id','=','boleta_referencia.es_id_destino')->join('paciente','paciente.pac_id','=','boleta_referencia.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->select('establecimiento_salud.es_id','es_nombre','es_nivel','paciente.pac_id','pac_grupo_sanguineo','pac_alergia','persona.per_id','persona.per_ci','persona.per_nombres','persona.per_apellido_primero','persona.per_apellido_segundo','boleta_referencia.br_id','br_cod','br_seguro','br_motivo','boleta_referencia.fe_id_origen','boleta_referencia.created_at')->get(); 

          return response()->json(["mensaje"=>"exito","referencia"=>$referencia],200);     
    }  

/**
 * @api {get} /referencias_establecimientos_destino realiza el listado de referencias por el campo es_id_destino 
 * @apiVersion 0.2.0
 * @apiName GetReferenciaDestino
 * @apiParam {Number} es_id ID del establecimiento de salud
 * @apiSuccess {array} referencia[]
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
  * @apiError No se encuentra un establecimiento con ese codigo
 */ 
    public function lista_referencia_destino($es_id_destino)
    {
        $establecimiento=Establecimiento_salud::find($es_id_destino);
         
          if (!$establecimiento)
        {
   return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un establecimiento con ese código.'])],404);
        }
$referencia=Boleta_referencia::where('es_id_destino',$es_id_destino)->join('establecimiento_salud','establecimiento_salud.es_id','=','boleta_referencia.es_id_origen')
->join('paciente','paciente.pac_id','=','boleta_referencia.pac_id')
->join('persona','persona.per_id','=','paciente.per_id')->select('establecimiento_salud.es_id','es_nombre','es_nivel','persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','per_ci','paciente.pac_id','pac_grupo_sanguineo','pac_alergia','boleta_referencia.br_id','boleta_referencia.es_id_destino','br_cod','br_seguro','br_estado_referencia','br_motivo','boleta_referencia.created_at')->get();  

return response()->json(['status'=>'ok','referencia'=>$referencia],200);

    }

/**
 * @api {post}/referencia Crea información para una referencia
 * @apiVersion 0.2.0
 * @apiName PostReferencia
 * @apiSuccess {Array} referencia.
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
            
            'pac_id' => 'required',
            'es_id_origen' => 'required',
            'es_id_destino' => 'required',
            'fe_id_origen'=> 'required', ]);

        if ($validator->fails()) 

        {
            return $validator->errors()->all();
        } 
        $referencia= new Boleta_referencia();
        $es_id_origen=$request->es_id_origen;
        $establecimiento=Establecimiento_salud::find($es_id_origen);
        $es_codigo=$establecimiento->es_codigo;
        $pac_id=$request->pac_id;
        $referencia->pac_id=$pac_id;
        $referencia->es_id_origen=$es_id_origen;
        $referencia->es_id_destino=$request->es_id_destino;
        $referencia->fe_id_origen=$request->fe_id_origen;
        $referencia->fe_id_destino=$request->fe_id_destino;
        $referencia->fe_id_contacto=$request->fe_id_contacto;
        $codigo=Boleta_referencia::generar_codigo($es_codigo,$pac_id);
        $referencia->br_cod=$codigo;
        $referencia->br_frec_cardiaca=$request->br_frec_cardiaca;
        $referencia->br_frec_resp=$request->br_frec_resp;
        $referencia->br_pa_sistolica=$request->br_pa_sistolica;
        $referencia->br_temperatura=$request->br_temperatura;
        $referencia->br_peso=$request->br_peso;
        $referencia->br_resumen=$request->br_resumen;
        $referencia->br_resultado_examen=$request->br_resultado_examen;
        $referencia->br_diagnostico=$request->br_diagnostico;
        $referencia->br_tratamiento_inicial=$request->br_tratamiento_inicial;
        $referencia->br_acomp=$request->br_acomp;
        $referencia->br_motivo=$request->br_motivo;
        $referencia->br_subsector=$request->br_subsector;
        $referencia->br_fecha_llegada=$request->br_fecha_llegada;
        $referencia->br_hora_llegada=$request->br_hora_llegada;
        $referencia->br_fecha_recepcion=$request->br_fecha_recepcion;
        $referencia->br_hora_recepcion=$request->br_hora_recepcion;
        $referencia->br_seguro=$request->br_seguro;
        $referencia->br_pa_diastolica=$request->br_pa_diastolica;
        $referencia->br_estado_referencia=$request->br_estado_referencia;
        $referencia->br_talla=$request->br_talla;
        $referencia->br_servicio_referente=$request->br_servicio_referente;
        $referencia->br_servicio_destino=$request->br_servicio_destino;
        $referencia->userid_at=JWTAuth::toUser()->id;
        $referencia->save();

        $paciente_es=Paciente_establecimiento::inactivo_paciente($pac_id,$es_id_origen);
        
        $resultado=compact('referencia','paciente_es');

        return response()->json(['status'=>'ok','mensaje'=>'exito','referencia'=>$resultado],200); 
    }

/**
 * @api {get} /referencia_contra verifica que una boleta de referencia tenga contrarefeencia
 * @apiVersion 0.2.0
 * @apiName GetReferenciaContra
 * @apiParam {Number} br_id ID de la boleta de referencia
 * @apiSuccess {array} contrareferencia[]
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
* @apiError No se encuentra una referencia con ese codigo
 */ 
     public function referencia_contra($br_id)
     
     {
        $referencia=Boleta_referencia::find($br_id);
        $contra='0';
        $bc_id=0;

         if (!$referencia)
        {
    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una referencia con ese código.'])],404);
        } 
        $contrareferencia = Boleta_contrareferencia::verifica_referencia($br_id);

        foreach ($contrareferencia as $contrareferencia) {
            $bc_id=$contrareferencia->bc_id;
        }

        if ($bc_id==0)
        {
           $contra=0;
        return response()->json(['status'=>'ok','contrareferencia'=>$contra],200); 
        }

         return response()->json(['status'=>'ok','contrareferencia'=>$contrareferencia],200);
         
        }

/**
 * @api {get} /referencia Obtiene los datos de una referencia
 * @apiVersion 0.2.0
 * @apiName GetReferencia
 * @apiParam {Number} br_id ID de la referencia
 * @apiSuccess {array} referencia
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra una referencia con ese codigo
 */
     public function show($br_id)
    {
        $referencia=Boleta_referencia::find($br_id);

         if (!$referencia)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una referencia con ese código.'])],404);
        }
        $pac_id=$referencia->pac_id;
        $es_id_origen=$referencia->es_id_origen;
        $es_id_destino=$referencia->es_id_destino;
        $fe_id_origen=$referencia->fe_id_origen;
        $fe_id_contacto=$referencia->fe_id_contacto;
        $paciente=Paciente::find($pac_id);
        $per_id=$paciente->per_id;

        $persona_paciente=Persona::find($per_id);

        $establecimiento_origen=Establecimiento_salud::find($es_id_origen);

        $establecimiento_destino=Establecimiento_salud::find($es_id_destino);

        $funcionario_origen=Funcionario_establecimiento::join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->select('funcionario_establecimiento.fe_id','fe_memorandum','fe_inicio_trabajo','fe_fin_trabajo','fe_cargo','fe_estado_laboral','fe_carga_laboral','persona.per_id','per_ci','per_ci_expedido','per_nombres','per_apellido_primero','per_apellido_segundo','per_fecha_nacimiento','per_genero','per_email','per_tipo_permanencia','per_numero_celular','per_clave_publica','per_tipo_documento','per_pais')->where('funcionario_establecimiento.fe_id',$fe_id_origen)->get();

        $direccion=Direccion::where('per_id',$per_id)->get();

        $red_id=$establecimiento_origen->red_id;

        $red=Red::find($red_id);
       
        if($fe_id_contacto!=null)
        { 
        $funcionario_contacto=Funcionario_establecimiento::join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->select('funcionario_establecimiento.fe_id','fe_memorandum','fe_inicio_trabajo','fe_fin_trabajo','fe_cargo','fe_estado_laboral','fe_carga_laboral','persona.per_id','per_ci','per_ci_expedido','per_nombres','per_apellido_primero','per_apellido_segundo','per_fecha_nacimiento','per_genero','per_email','per_tipo_permanencia','per_numero_celular','per_clave_publica','per_tipo_documento','per_pais')->where('funcionario_establecimiento.fe_id',$fe_id_contacto)->get(); 
        }

        $referencia=compact('referencia','persona_paciente','paciente','direccion','establecimiento_origen','red','establecimiento_destino','funcionario_origen','funcionario_contacto');

         return response()->json(['status'=>'ok','referencia'=>$referencia],200);  
     }
/**
 * @api {get} /establecimientos_referencia Obtiene lista de establecimientos destino
 * @apiVersion 0.2.0
 * @apiName GetReferenciaEstablecimientos
 * @apiParam {Number} es_id ID del establecimiento
 * @apiSuccess {array} establecimiento[]
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 * @apiError No se encuentra un establecimiento con ese codigo
 */ 
    
    public function listar_establecimientos_referencia($es_id)
    {
$establecimiento_salud=Establecimiento_salud::find($es_id);

if (!$establecimiento_salud)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un establecimiento con ese codigo'])],404);
        }

$es_nivel=$establecimiento_salud->es_nivel;

if($es_nivel=='PRIMER NIVEL')
{
$establecimiento = Establecimiento_salud::select('establecimiento_salud.es_id','es_nombre','es_nivel')->where('es_nivel','like','SEGUNDO NIVEL')->orderBy('es_nombre')->get();

$establecimiento1 = Establecimiento_salud::select('establecimiento_salud.es_id','es_nombre','es_nivel')->where('es_nivel','like','TERCER NIVEL')->orderBy('es_nombre')->get();

$establecimiento2 = Establecimiento_salud::select('establecimiento_salud.es_id','es_nombre','es_nivel','tipo.tip_nombre')->join('tipo','tipo.tip_id','=','establecimiento_salud.tip_id')->where('tipo.tip_id','=',5)->whereNotIn('establecimiento_salud.es_id',[$es_id])->orderBy('es_nombre')->get();

$resultado=compact('establecimiento','establecimiento1','establecimiento2');
 
return response()->json(['status'=>'ok','establecimiento'=>$resultado],200); 
}
if($es_nivel=='SEGUNDO NIVEL')
{
    $establecimiento = Establecimiento_salud::select('establecimiento_salud.es_id','es_nombre','es_nivel')->where('es_nivel','like','SEGUNDO NIVEL')->whereNotIn('establecimiento_salud.es_id',[$es_id])->orderBy('es_nombre')->get();

$establecimiento1 = Establecimiento_salud::select('establecimiento_salud.es_id','es_nombre','es_nivel')->where('es_nivel','like','TERCER NIVEL')->orderBy('es_nombre')->get();

$resultado=compact('establecimiento','establecimiento1');
 
return response()->json(['status'=>'ok','establecimiento'=>$resultado],200); 
}
  
if($es_nivel=='TERCER NIVEL')
{
$establecimiento1 = Establecimiento_salud::select('establecimiento_salud.es_id','es_nombre','es_nivel')->where('es_nivel','like','TERCER NIVEL')->whereNotIn('establecimiento_salud.es_id',[$es_id])->orderBy('es_nombre')->get();
 
return response()->json(['status'=>'ok','establecimiento'=>$establecimiento1],200); 
}
     
}  
/**
 * @api {get} /estado_referencia_destino Obtiene las referencias en el que el campo estado sea igual a true por el establecimiento destino
 * @apiVersion 0.2.0
 * @apiName GetReferenciaEstado
 * @apiParam {Number} es_id ID del establecimiento
 * @apiSuccess {array} referencia[]
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 */ 
public function estado_referencia_destino($es_id_destino)

{
$referencia=Boleta_referencia::where('es_id_destino',$es_id_destino)->join('boleta_contrareferencia','boleta_contrareferencia.br_id','=','boleta_referencia.br_id')->select('boleta_referencia.br_id');

$nocontra=Boleta_referencia::where('br_estado_referencia','T')->where('es_id_destino',$es_id_destino)->join('establecimiento_salud','establecimiento_salud.es_id','=','boleta_referencia.es_id_origen')
    ->join('paciente','paciente.pac_id','=','boleta_referencia.pac_id')
    ->join('persona','persona.per_id','=','paciente.per_id') ->select('establecimiento_salud.es_id','es_nombre','es_nivel','paciente.pac_id','pac_grupo_sanguineo','pac_alergia','persona.per_id','persona.per_ci','persona.per_nombres','persona.per_apellido_primero','persona.per_apellido_segundo','boleta_referencia.br_id','br_cod','br_seguro','br_motivo','boleta_referencia.created_at')->whereNotIn('boleta_referencia.br_id',$referencia)->get();

return response()->json(['status'=>'ok','referencia'=>$nocontra],200); 

}
/**
 * @api {get} /red_referencias Obtiene los establecimientos de salud de una red que le pertenecen al es_id entrante
 * @apiVersion 0.2.0
 * @apiName GetReferenciasRed
 * @apiParam {Number} es_id ID del establecimiento
 * @apiSuccess {array} establecimiento[]
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
* @apiError No se encuentra un establecimiento con ese codigo
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "EstablecimientoNotFound"
 *     }
 */ 
public function red_referencias($es_id)
{
    $establecimiento_salud=Establecimiento_salud::find($es_id);

if (!$establecimiento_salud)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un establecimiento con ese codigo'])],404);
        }
$red_id=$establecimiento_salud->red_id;

$establecimiento=Establecimiento_salud::where('red_id',$red_id)
->whereNotIn('establecimiento_salud.es_id',[$es_id])->get(['es_id','es_nombre','es_nivel']);

return response()->json(['status'=>'ok','establecimiento'=>$establecimiento],200); 

}
/**
 * @api {put} /referencia/:br_id modifica los campos de la boleta de referencia
 * @apiVersion 0.2.0
 * @apiName PutReferencia
 
 * @apiParam {Number} br_id referencia unique ID.
 * @apiSuccess {Array} referencia
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     { *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra una referencia con ese código.'
 */
    public function update(Request $request, $br_id)
    {
        $referencia=Boleta_referencia::find($br_id);
         $input = $request->all();

         if (!$referencia)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una referencia con ese código.'])],404);
        }
        $referencia->update($input);
        $referencia=Boleta_referencia::find($br_id);
        return response()->json(['status'=>'ok','mensaje'=>'exito','referencia'=>$referencia],200);
    }
/**
 * @api {put} /referencias_estados/:br_id // realiza la edicion del campo estado referencia esto para la aceptacion de una boleta de referencia, y si el paciente no esta registrado en el establecimiento destino le asigna numero de historial clinico
 * @apiVersion 0.2.0
 * @apiName PutReferenciaEstado
 * @apiParam {Number} br_id referencia unique ID.
 * @apiSuccess {Array} referencia
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     { *       "status": ok,
 *       "mensaje": exito
 *     }
 * @apiError 'No se encuentra una referencia con ese código.
 */
     public function editar(Request $request, $br_id)
    {
        $referencia=Boleta_referencia::find($br_id);

         if (!$referencia)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una referencia con ese código.'])],404);
        }

        $pe_hist_clinico=$request->pe_hist_clinico;
        $pac_id=$referencia->pac_id;
        $es_id=$request->es_id;
        $referencia->fe_id_destino=$request->fe_id_destino;
        $referencia->br_fecha_llegada=$request->br_fecha_llegada;
        $referencia->br_hora_llegada=$request->br_hora_llegada;
        $referencia->br_estado_referencia=$request->br_estado_referencia;
        $referencia->save();
        
        if($pe_hist_clinico!=null)

        {  $userid_at=JWTAuth::toUser()->id;
            $paciente_establecimiento=Paciente_establecimiento::crear_paciente($es_id,$pac_id,$pe_hist_clinico,$userid_at); 
        $resultado=compact('referencia','paciente_establecimiento');

        return response()->json(['status'=>'ok','mensaje'=>'exito','referencia'=>$resultado],200);
        }
    $paciente_es= Paciente_establecimiento::activar_paciente($pac_id,$es_id);

        $resultado=compact('referencia','paciente_es');

        return response()->json(['status'=>'ok','mensaje'=>'exito','referencia'=>$resultado],200);
    }
/**
 * @api {delete} /referencia/:br_id Elimina una boleta de referencia
 * @apiVersion 0.2.0
 * @apiName DeleteReferencia
 * @apiParam {Number} br_id referencia unique ID.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": registros eliminados correctamente
 *     }
 *
 * @apiError No se encuentra una referencia con ese código.
 */
    public function destroy($br_id)
    {
         $referencia = Boleta_referencia::find($br_id);
          if (!$referencia)
        {
return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una referencia con ese código.'])],404);
        }
$contrareferencia = Boleta_contrareferencia::where('br_id',$br_id)->get()->first();
$count= count($contrareferencia);
if ($count<=0)
        { $referencia->delete();
             return response()->json(["mensaje" => "registro eliminado correctamente" ], 200);} 
         $referencia->delete();
         $contrareferencia->delete();
        return response()->json([
            "mensaje" => "registros eliminados correctamente"
            ], 200
        );
    }
}
