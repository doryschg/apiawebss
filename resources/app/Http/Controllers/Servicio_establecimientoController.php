<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Validator;
use Illuminate\Support\Str;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon;
use awebss\Models\Servicio_establecimiento;
use awebss\Models\Establecimiento_salud;

class Servicio_establecimientoController extends Controller
{

     public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }


    // lista a los funcionarios que prestan un determinado servicio 
/**
 * @api {get} /servicios_establecimientos Obtiene llos funcionarios que prestan un servicio
 * @apiVersion 0.2.0
 * @apiName GetFuncionario
 * @apiSuccess {array} funcionario_establecimiento
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError funcionario_establecimiento Array[]
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "FuncionarioNotFound"
 *     }
 */ 
    public function index(Request $request)
    {

     $validator = Validator::make($request->all(), [
            
            'se_id' => 'required', ]);

        if ($validator->fails()) 
        {
            return $validator->errors()->all();
        } 

    $fecha=Carbon::now();

    $se_id=$request->se_id;
    
    $funcionario_establecimiento=\awebss\Models\Servicio_consultorio::where('se_id',$se_id)->join('configuracion_horario','configuracion_horario.sc_id','=','servicio_consultorio.sc_id')->join('configuracion_turno','configuracion_turno.ch_id','=','configuracion_horario.ch_id')->join('atiende_diariamente','atiende_diariamente.ct_id','=','configuracion_turno.ct_id')->join('funcionario_establecimiento','funcionario_establecimiento.fe_id','=','configuracion_horario.fe_id')->join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->select('funcionario_establecimiento.fe_id','persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','configuracion_horario.ch_id','configuracion_turno.ct_turno')->where('atiende_diariamente.ad_fecha_atiende',$fecha)->distinct('configuracion_horario.ch_id')->get();

    return response()->json(['status'=>'ok','funcionario'=>$funcionario_establecimiento],200); 
    }
/**
 * @api {get} /establecimiento_presta Obtiene la lista de servicios que presta un establecimiento
 * @apiVersion 0.2.0
 * @apiName GetServicio
 * @apiParam {Number} es_id ID del establecimiento de salud
 * @apiSuccess {array} servicio_establecimiento
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un establecimiento con ese código
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ServicioNotFound"
 *     }
 */ 
    public function listar_servicios($es_id)
    {
        $establecimiento= Establecimiento_salud::find($es_id);

    if (!$establecimiento)
    {
        return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un establecimiento con ese código.'])],404);
    }
$servicio_establecimiento=Servicio_establecimiento::where('es_id',$es_id)->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->select('servicio_establecimiento.se_id','es_id','se_necesita_ref','servicio.ser_id','ser_nombre','ser_tipo')->orderBy('ser_tipo')->get();

 return response()->json(['status'=>'ok','servicio_especialidad'=>$servicio_establecimiento],200); 
    }
/**
 * @api {get} /servicios_no_referencias Obtiene la lista de servicios que no requieren referenciacion
 * @apiVersion 0.2.0
 * @apiName GetServicio
 * @apiParam {Number} es_id ID del establecimiento de salud
 * @apiSuccess {array} servicio_establecimiento
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un establecimiento con ese código
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ServicioNotFound"
 *     }
 */ 
public function servicios_que_no_requieren_referenciacion($es_id)
    {
        $establecimiento= Establecimiento_salud::find($es_id);

    if (!$establecimiento)
    {
        return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un establecimiento con ese código.'])],404);
    }
$servicio_establecimiento=\awebss\Models\Servicio_establecimiento::where('es_id',$es_id)->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->where('servicio_establecimiento.se_necesita_ref','false')->select('servicio_establecimiento.se_id','es_id','se_necesita_ref','servicio.ser_id','ser_nombre','ser_tipo')->orderBy('ser_nombre')->get();

 return response()->json(['status'=>'ok','servicio_establecimiento'=>$servicio_establecimiento],200); 
    }
/**
 * @api {get} /establecimientos_servicios Obtiene los establecimientos que prestan un servicio
 * @apiVersion 0.2.0
 * @apiName GetServicio
 * @apiParam {Number} ser_id ID del servicio
 * @apiSuccess {array} servicio_establecimiento
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un servicio con ese código
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ServicioNotFound"
 *     }
 */ 
 public function establecimiento_salud($ser_id)
    {
$servicios= \awebss\Models\Servicio::find($ser_id);

    if (!$servicios)
    {
        return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un servicio con ese código.'])],404);
    }
$servicio_establecimiento=Servicio_establecimiento::where('ser_id',$ser_id)->join('establecimiento_salud','establecimiento_salud.es_id','=','servicio_establecimiento.es_id')->select('servicio_establecimiento.se_id','se_necesita_ref','establecimiento_salud.es_id','es_nombre','es_nivel')->get();

 return response()->json(['status'=>'ok','servicio_establecimiento'=>$servicio_establecimiento],200); 

}

/**
 * @api {get} /servicios_no_establecimientos Obtiene la lista de servicios que no tiene el establecimiento de salud
 * @apiVersion 0.2.0
 * @apiName GetServicio
 * @apiParam {Number} es_id ID del establecimiento de salud
 * @apiSuccess {array} servicio_establecimiento
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un establecimiento con ese código
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ServicioNotFound"
 *     }
 */

public function agregar_servicios($es_id)
{
$establecimientos=Establecimiento_salud::find($es_id);

        if (!$establecimientos)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra el establecimientos con ese código.'])],404);
        }
$servicio_establecimiento=Servicio_establecimiento::where('es_id',$es_id)->select('ser_id')->get();

$nopresta=\awebss\Models\Servicio::select('ser_id','ser_nombre','ser_tipo')->whereNotIn('ser_id',$servicio_establecimiento)->orderBy('ser_nombre')->get();
return response()->json(['status'=>'ok','nopresta'=>$nopresta],200); 
}
/**
 * @api {post}/servicios_establecimientos Crea información del servicio que presta un establecimiento de salud.
 * @apiVersion 0.2.0
 * @apiName PostPaciente
 * @apiSuccess {Array} servicio_establecimiento
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
            
            'es_id' => 'required',
            'ser_id' => 'required', ]);

        if ($validator->fails()) 
        {
            return $validator->errors()->all();
        }  
        $servicio_establecimiento= new \awebss\Models\Servicio_establecimiento();
        $servicio_establecimiento->se_necesita_ref=Str::upper($request->se_necesita_ref);
        $servicio_establecimiento->se_costo=$request->se_costo;
        $servicio_establecimiento->es_id=$request->es_id;
        $servicio_establecimiento->ser_id=$request->ser_id;
        $servicio_establecimiento->userid_at=JWTAuth::toUser()->id;
        $servicio_establecimiento->save();
        
        return response()->json(['mensaje'=>'exito','servicio_establecimiento'=>$servicio_establecimiento],200);
    }
/**
 * @api {get} /servicios_establecimientos Obtiene la informacion del servicio de un establecimiento
 * @apiVersion 0.2.0
 * @apiName GetServicio
 * @apiParam {Number} se_id ID del servicio establecimiento
 * @apiSuccess {array} servicio_establecimiento
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un servicio con ese código
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ServicioNotFound"
 *     }
 */
 
    public function show($se_id)
    {
        $servicio_establecimiento=Servicio_establecimiento::find($se_id);
       
       if (!$servicio_establecimiento)
        {
    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un servicio con ese código.'])],404);
        }

        $servicio_establecimiento=\awebss\Models\Servicio_establecimiento::where('se_id',$se_id)->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->select('servicio_establecimiento.se_id','se_costo','se_necesita_ref','servicio.ser_id','ser_nombre')->get();

       return response()->json(['mensaje'=>'exito','servicio_establecimiento'=>$servicio_establecimiento],200);
    }
/**
 * @api {put} /servicios_establecimientos/:se_id modifica los campos de un servicio del establecimiento
 * @apiVersion 0.2.0
 * @apiName PutServicio
 * @apiParam {Number} se_id servicio_establecimiento unique ID.
 * @apiSuccess {Array} paciente
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra un servicio con ese código.'
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ServicioNotFound"
 *     }
 */ 
    public function update(Request $request, $se_id)
    {
         $servicio_establecimiento= Servicio_establecimiento::find($se_id);

          if (!$servicio_establecimiento)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un servicio con ese código.'])],404);
        }
        $servicio_establecimiento->se_necesita_ref=$request->se_necesita_ref;
        $servicio_establecimiento->se_costo=$request->se_costo;
        $servicio_establecimiento->ser_id=$request->ser_id;
        $servicio_establecimiento->save();
       
        return response()->json(['mensaje'=>'exito','servicios_establecimiento'=>$servicio_establecimiento],200);   
    }
/**
 * @api {delete} /servicios_establecimientos/:se_id Elimina un servicio de un consultorio
 * @apiVersion 0.2.0
 * @apiName DeleteServicio
 *
 * @apiParam {Number} se_id Servicio_establecimiento unique ID.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": registros eliminados correctamente
 *     }
 *
 * @apiError No se encuentra un servicio con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ConsultorioNotFound"
 *     }
 */

     
 public function destroy($se_id)
    {
        $servicio_establecimiento =Servicio_establecimiento::find($se_id);
         if (!$servicio_establecimiento)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un servicio con ese código.'])],404);
        }
        $servicio_establecimiento->delete();
        return response()->json([
            "mensaje" => "registros eliminados correctamente"
            ], 200
        );
    }
}
