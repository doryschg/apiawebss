<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class Configuracion_horarioController extends Controller
{

 public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }
     
 /**
 * @api {get} /configuracion_horario Obtiene las configuraciones de horario vigentes de un establecimiento de salud
 * @apiVersion 0.2.0
 * @apiName GetConfiguracionHoracioVigente
 * @apiSuccess {Array} configuracion_horario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError No se encuentran configuraciones vigentes.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ConfiguracionNotFound"
 *     }
 */
  public function index(Request $request)
    {

    $validator = Validator::make($request->all(), [
            
            'es_id' => 'required',
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }

    $es_id=$request->es_id;
    $fecha=Carbon::now();        
    $configuracion_horario=\awebss\Models\Configuracion_horario::select('configuracion_horario.ch_id','ch_fecha_inicio','ch_fecha_final','funcionario_establecimiento.fe_id','persona.per_id','per_nombres','per_apellido_primero','consultorio.con_id','con_nombre','servicio.ser_id','ser_nombre')->join('funcionario_establecimiento','funcionario_establecimiento.fe_id','=','configuracion_horario.fe_id')->join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','configuracion_horario.sc_id')->join('consultorio','consultorio.con_id','=','servicio_consultorio.con_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->join('persona','persona.per_id','=','funcionario.per_id')->where('funcionario_establecimiento.es_id',$es_id)->where('configuracion_horario.ch_fecha_final','>=',$fecha)->get();
 
  $count=count($configuracion_horario);

   /* if($count<=0)
    {

    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentran configuraciones de horarios.'])],404);

    } */

  return response()->json(['status'=>'ok','mensaje'=>'exito','configuracion_horario'=>$configuracion_horario],200);

    }

 /**
 * @api {get} /horarios_establecimientos Obtiene las configuraciones de horario de un establecimiento de salud
 * @apiVersion 0.2.0
 * @apiName GetConfiguracionHorario
 * @apiParam {Number} es_id id del establecimiento de salud
 * @apiSuccess {Array} configuracion_horario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError No se encuentran configuraciones vigentes.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ConfiguracionNotFound"
 *     }
 */
    public function listar_configuracion_horarios($es_id)
    {

  $configuracion_horario=\awebss\Models\Configuracion_horario::select('configuracion_horario.ch_id','ch_fecha_inicio','ch_fecha_final','funcionario_establecimiento.fe_id','persona.per_id','per_nombres','per_apellido_primero','consultorio.con_id','con_nombre','servicio.ser_id','ser_nombre')->join('funcionario_establecimiento','funcionario_establecimiento.fe_id','=','configuracion_horario.fe_id')->join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','configuracion_horario.sc_id')->join('consultorio','consultorio.con_id','=','servicio_consultorio.con_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->join('persona','persona.per_id','=','funcionario.per_id')->where('funcionario_establecimiento.es_id',$es_id)->get();

 $count=count($configuracion_horario);

   /* if($count<=0)
    {

    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentran configuraciones de horarios.'])],404);

    }*/

  return response()->json(['status'=>'ok','mensaje'=>'exito','configuracion_horario'=>$configuracion_horario],200);

    }

/**
 * @api {get} /horarios_consultorios Obtiene los turnos de consultorios
 * @apiVersion 0.2.0
 * @apiName GetHorarioConsultorio
 * @apiParam {Number} con_id id del consultorio
 * @apiSuccess {Array} configuracion_horario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {"status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError No se encuentran turnos para ese consultorio.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "TurnosNotFound"
 *     }
 */

    public function listar_turnos_por_consultorio($con_id)
    {

    $fecha_actual=Carbon::now();

    $configuracion_horario=\awebss\Models\Configuracion_horario::select('configuracion_turno.ct_id','ct_dia','ct_turno','ct_ini_turno','ct_fin_turno','ct_ficha_total','ct_ficha_sesar','configuracion_horario.ch_id','persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','servicio_consultorio.sc_id','servicio.ser_id','ser_nombre')->join('configuracion_turno','configuracion_turno.ch_id','=','configuracion_horario.ch_id')->join('funcionario_establecimiento','funcionario_establecimiento.fe_id','=','configuracion_horario.fe_id')->join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','configuracion_horario.sc_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->where('servicio_consultorio.con_id',$con_id)->where('configuracion_horario.ch_fecha_final','>=',$fecha_actual)->where('configuracion_horario.ch_fecha_inicio','<=',$fecha_actual)->get();

     $count=count($configuracion_horario);

    if($count<=0)
    {

    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentran turnos para ese consultorio.'])],404);

    }
        return response()->json(['status'=>'ok','mensaje'=>'exito','configuracion_turno'=>$configuracion_horario],200);
    }
 /**
 * @api {post} /configuracion_horario Crea información para una configuracion de horario
 * @apiVersion 0.2.0
 * @apiName PostConfiguracionHorario
 * @apiSuccess {Number} code  Código 0 conforme todo ha ido bien.
 * @apiSuccess {Array} configuracion_horario.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError Configuracion_horarioBadResponse La configuración de horario no ha podido crearse
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
            
            'fe_id' => 'required',
            'sc_id' => 'required',
            'ch_fecha_inicio'=>'required',
            'ch_fecha_final'=>'required',
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }

        $configuracion_horario = new \awebss\Models\Configuracion_horario();
        $configuracion_horario->ch_fecha_inicio=$request->ch_fecha_inicio;
        $configuracion_horario->ch_fecha_final=$request->ch_fecha_final;
        $configuracion_horario->fe_id=$request->fe_id;
        $configuracion_horario->sc_id=$request->sc_id;
        $configuracion_horario->userid_at=JWTAuth::toUser()->id;
        $configuracion_horario->save();

        return response()->json(['status'=>'ok','mensaje'=>'exito','configuracion_horario'=>$configuracion_horario],200); 
    }

 /**
 * @api {get} /configuracion_horario Obtiene la informacion de una configuración de horario
 * @apiVersion 0.2.0
 * @apiName GetConfiguracion_Horario
 * @apiParam {Number} ch_id id de la configuracion de horario
 * @apiSuccess {Array} configuracion_horario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError No se encuentra una configuracion de horario con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "Configuracion_horarioNotFound"
 *     }
 */
    public function show($ch_id)
    {
         $configuracion_horario=\awebss\Models\Configuracion_horario::find($ch_id);

         if (!$configuracion_horario)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una configuracion horario con ese código.'])],404);
        }

        $configuracion_turno=\awebss\Models\Configuracion_turno::where('ch_id',$ch_id)->get();

        $sc_id=$configuracion_horario->sc_id;

        $servicio_consultorio=\awebss\Models\Servicio_consultorio::select('consultorio.con_id','con_nombre','servicio_establecimiento.se_id','servicio.ser_id','ser_nombre')->where('servicio_consultorio.sc_id',$sc_id)->join('consultorio','consultorio.con_id','=','servicio_consultorio.con_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->get();
        
        $fe_id=$configuracion_horario->fe_id;

        $funcionario_establecimiento=\awebss\Models\Funcionario_establecimiento::select('funcionario_establecimiento.fe_id','funcionario.fun_id','fun_profesion','persona.per_id','per_nombres','per_ci','per_apellido_primero','per_apellido_segundo')->where('funcionario_establecimiento.fe_id',$fe_id)->join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->get();
        
        $resultado=compact('configuracion_horario','servicio_consultorio','funcionario_establecimiento','configuracion_turno');

        return response()->json(['status'=>'ok','mensaje'=>'exito','horario'=>$resultado],200); 
    }

/**
 * @api {put} /configuracion_horario/:ch_id duplica configuraciones de horario. 
 * @apiVersion 0.2.0
 * @apiName PutConfiguracionHorario
 
 * @apiParam {Number} ch_id Configuracion_horario unique ID.
 *
 * @apiSuccess {Number} code  Código 0 conforme todo ha ido bien.
 * @apiSuccess {Array} configuracion_horario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra una configuracion horario con ese código.'
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "Configuracion_horaioNotFound"
 *     }
 */ 
    public function update(Request $request, $ch_id)
    {

   $configuracion_horario=\awebss\Models\Configuracion_horario::find($ch_id);

     if (!$configuracion_horario)
     {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una configuracion horario con ese código.'])],404);
     }

    $fecha_final=$request->ch_fecha_final;
    $ch_fecha_inicio= new \Carbon\Carbon($configuracion_horario->ch_fecha_final);

        $fecha_inicio=$ch_fecha_inicio->addDay(1);

        $horario_replica= new \awebss\Models\Configuracion_horario();
        $horario_replica->ch_fecha_inicio=$fecha_inicio;
        $horario_replica->ch_fecha_final=$fecha_final;
        $horario_replica->fe_id=$configuracion_horario->fe_id;
        $horario_replica->sc_id=$configuracion_horario->sc_id;
        $horario_replica->userid_at=JWTAuth::toUser()->id;
        $horario_replica->save();

        return response()->json(['status'=>'ok','mensaje'=>'exito','configuracion_horario'=>$horario_replica],200);
    }

/**
 * @api {delete} /configuracion_horario/:ch_id Elimina una configuracion de horario en cascada
 * @apiVersion 0.2.0
 * @apiName DeleteConfiguraciónHorario
 *
 * @apiParam {Number} ch_id Configuracion_horario unique ID.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": registros eliminados correctamente
 *     }
 *
 * @apiError No se encuentra una configuracion horario con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "Configuracion_horarioNotFound"
 *     }
 */

    public function destroy($ch_id)
    {
        $configuracion_horario=\awebss\Models\Configuracion_horario::find($ch_id);
         if (!$configuracion_horario)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una configuracion horario con ese código.'])],404);
        }
        $configuracion_turno=\awebss\Models\Configuracion_turno::where('ch_id',$ch_id)->get();
        
        foreach ($configuracion_turno as $turno) {
            $ct_id=$turno->ct_id;
            
            $atiende_diariamente=\awebss\Models\Atiende_diariamente::where('ct_id',$ct_id)->get();
            foreach ($atiende_diariamente as $diariamente) {
                        
                        $diariamente->delete();
                    }   
                    $turno->delete();
                         }

        $configuracion_horario->delete();
        return response()->json([ "mensaje" => "registros eliminados correctamente" ], 200);
    }
}
