<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;
use awebss\Http\Requests;
use Carbon;
use Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CitaController extends Controller
{ 
    public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','confirmar_cita','destroy']]);
    }
     
/**
 * @api {post}/citas Crea información para una cita medica
 * @apiVersion 0.2.0
 * @apiName PostCita
 * @apiSuccess {Array} cita.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError BadResponse La cita no ha podido crearse
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
            
            'ad_id' => 'required',
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }
        
        $ad_id=$request->ad_id;
        $atiende_diariamente=\awebss\Models\Atiende_diariamente::find($ad_id);
        $cit_fecha_consulta=$atiende_diariamente->ad_fecha_atiende;
        $cit_hora_consulta=$atiende_diariamente->ad_hora_inicio;
        $ct_id=$atiende_diariamente->ct_id;
        $cit_nro_ficha=$atiende_diariamente->ad_numero_ficha;
        $turno=\awebss\Models\Configuracion_turno::find($ct_id);
        $ch_id=$turno->ch_id;
        $horario=\awebss\Models\Configuracion_horario::find($ch_id);
        $cit_fe_id=$horario->fe_id;
        $cita= new \awebss\Models\Cita();
        $cita->ad_id=$ad_id;
        $cita->pac_id=$request->pac_id;
        $cita->cit_fecha_consulta=$cit_fecha_consulta;
        $cita->cit_hora_consulta=$cit_hora_consulta;
        $cita->cit_nro_ficha=$cit_nro_ficha;
        $cita->cit_motivo_consulta=$request->cit_motivo_consulta;
        $cita->cit_estado_asistencia=$request->cit_estado_asistencia;
        $cita->cit_estado_pago=$request->cit_estado_pago;
        $cita->cit_estado_confirmacion=$request->cit_estado_confirmacion;
        $cita->cit_tipo=$request->cit_tipo;
        $cita->cit_es_id=$request->cit_es_id;
        $cita->cit_se_id=$request->cit_se_id;
        $cita->cit_con_id=$request->cit_con_id;
        $cita->cit_fe_id=$cit_fe_id;
        $cita->cit_calificar_es='SIN CALIFICACION';
        $cita->cit_calificar_se='SIN CALIFICACION';
        $cita->cit_calificar_con='SIN CALIFICACION';
        $cita->cit_calificar_fe='SIN CALIFICACION';
        $cita->userid_at=JWTAuth::toUser()->id;
        $cita->save();

        if($cita->cit_tipo=='PROGRAMADA')
        {

return response()->json(['status'=>'ok','mensaje'=>'exito','cita'=>$cita],200); 

        }
        else
        {
        
        $atiende_diariamente=\awebss\Models\Atiende_diariamente::find($ad_id);
        $atiende_diariamente->ad_estado='OCUPADO';

        $atiende_diariamente->save();
        $resultado=compact('cita','atiende_diariamente');
        return response()->json(['status'=>'ok','mensaje'=>'exito','cita'=>$resultado],200); 
        }

    }

 /**
 * @api {get} /citas Obtiene la informacion de una cita
 * @apiVersion 0.2.0
 * @apiName GetCita
 * @apiParam {Number} cit_id ID de la cita
 * @apiSuccess {array} cita
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra una cita con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "CitaNotFound"
 *     }
 */ 
    public function show($cit_id)
    {
        $cita=\awebss\Models\Cita::find($cit_id);

          if (!$cita)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una cita con ese código.'])],404);
        }

        return response()->json(['status'=>'ok','mensaje'=>'exito','cita'=>$cita],200); 
    }

 /**
 * @api {get} /citas_establecimientos Obtiene todas las citas confirmadas de un establecimiento de salud dado una fecha
 * @apiVersion 0.2.0
 * @apiName GetCitaFecha
 * @apiParam {Number} es_id ID del establecimiento
 * @apiSuccess {array} cita
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentran citas
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "CitaNotFound"
 *     }
 */ 
       public function listar_citas_fechas(Request $request, $es_id)
    {

         $validator = Validator::make($request->all(), [
            
            'fecha' => 'required',
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }
        
        $fecha=$request->fecha;

        $cita=\awebss\Models\Cita::where('cit_es_id',$es_id)->where('cit_fecha_consulta',$fecha)->join('paciente','paciente.pac_id','=','cita.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('consultorio','consultorio.con_id','=','cita.cit_con_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','cita.cit_se_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->select('persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','consultorio.con_id','con_nombre','servicio.ser_id','ser_nombre','cita.cit_id','cit_fecha_consulta','cit_hora_consulta','cit_nro_ficha','cit_motivo_consulta','cit_estado_asistencia','cit_estado_pago','cit_tipo','cit_estado_confirmacion')->where('cita.cit_estado_confirmacion',TRUE)->get();

        return response()->json(['status'=>'ok','mensaje'=>'exito','cita'=>$cita],200); 
        
    }

/**
 * @api {get} /reservas_pacientes Obtiene las citas medicas de los pacientes.
 * @apiVersion 0.2.0
 * @apiName GetCitaPaciente
 * @apiParam {Number} pac_id ID del paciente
 * @apiSuccess {array} cita
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentran citas
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "CitaNotFound"
 *     }
 */ 

     public function listar_citas_paciente(Request $request, $pac_id)
    {
         $validator = Validator::make($request->all(), [
            
            'nro' => 'required',
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }

        $nro=$request->nro;

        $cita=\awebss\Models\Cita::where('paciente.pac_id',$pac_id)->join('paciente','paciente.pac_id','=','cita.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('consultorio','consultorio.con_id','=','cita.cit_con_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','cita.cit_se_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->select('persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','paciente.pac_id','consultorio.con_id','con_nombre','servicio.ser_id','ser_nombre','cita.cit_id', 'cit_fecha_consulta','cit_hora_consulta','cit_nro_ficha','cit_motivo_consulta','cit_estado_asistencia','cit_estado_pago','cit_tipo','cit_estado_confirmacion')->orderBy('cita.cit_fecha_consulta','desc')->paginate($nro);

        return response()->json(['status'=>'ok','mensaje'=>'exito','cita'=>$cita],200); 
    }
/**
 * @api {get} /reservas_medicos Obtiene las citas de un medico.
 * @apiVersion 0.2.0
 * @apiName GetCitaMedico
 * @apiParam {Number} fe_id ID del funcionario establecimiento
 * @apiSuccess {array} cita
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentran citas
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "CitaNotFound"
 *     }
 */ 
    public function listar_citas_medico(Request $request, $fe_id)
    {
        $validator = Validator::make($request->all(), [
            
            'nro' => 'required',
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }

        $nro=$request->nro;
        
        $cita=\awebss\Models\Cita::where('cit_fe_id',$fe_id)->join('paciente','paciente.pac_id','=','cita.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('consultorio','consultorio.con_id','=','cita.cit_con_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','cita.cit_se_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->where('cita.cit_tipo','PROGRAMADA')->select('persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','consultorio.con_id','con_nombre','servicio.ser_id','ser_nombre','cita.cit_id', 'cit_fecha_consulta','cit_hora_consulta','cit_nro_ficha','cit_motivo_consulta','cit_estado_asistencia','cit_estado_pago','cit_tipo','cit_estado_confirmacion','cita.pac_id')->orderBy('cita.cit_fecha_consulta','desc')->paginate($nro);

        return response()->json(['status'=>'ok','mensaje'=>'exito','cita'=>$cita],200);    
    }

/**
 * @api {get} /citas_medico Obtiene las citas de un medico dado una fecha.
 * @apiVersion 0.2.0
 * @apiName GetCitaMedico
 * @apiParam {Number} fe_id ID del funcionario establecimiento
 * @apiSuccess {array} cita
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentran citas
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "CitaNotFound"
 *     }
 */ 
     public function listar_citas_medico_fecha(Request $request,$fe_id)
    
    {
         $validator = Validator::make($request->all(), [
            
            'fecha' => 'required',
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }

        $fecha=$request->fecha;
      
        $cita=\awebss\Models\Cita::where('cit_fe_id',$fe_id)->join('paciente','paciente.pac_id','=','cita.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('consultorio','consultorio.con_id','=','cita.cit_con_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','cita.cit_se_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->where('cit_fecha_consulta',$fecha)->select('persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','consultorio.con_id','con_nombre','servicio.ser_id','ser_nombre','cita.cit_id', 'cit_fecha_consulta','cit_hora_consulta','cit_nro_ficha','cit_motivo_consulta','cit_estado_asistencia','cit_estado_pago','cit_tipo','cit_estado_confirmacion','cita.pac_id')->where('cita.cit_estado_confirmacion',TRUE)->get();

    return response()->json(['status'=>'ok','mensaje'=>'exito','cita'=>$cita],200); 
        
    }
/**
 * @api {get} /citas_medico Obtiene las citas confirmadas de un dia de todos los consultorios.
 * @apiVersion 0.2.0
 * @apiName GetCitaDia
 * @apiParam {Number} es_id ID del establecimiento de salud
 * @apiSuccess {array} cita
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentran citas
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "CitaNotFound"
 *     }
 */ 

     public function listar_citas_dia($es_id)
    {

        $fecha=Carbon::now();

        $fecha=$fecha->format('Y-m-d');

       $cita=\awebss\Models\Cita::where('cit_es_id',$es_id)->where('cit_fecha_consulta',$fecha)->join('paciente','paciente.pac_id','=','cita.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('consultorio','consultorio.con_id','=','cita.cit_con_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','cita.cit_se_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->where('cita.cit_estado_confirmacion',TRUE)->get(['persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','consultorio.con_id','con_nombre','servicio.ser_id','ser_nombre','cita.cit_id' ,'cit_fecha_consulta','cit_hora_consulta','cit_nro_ficha','cit_motivo_consulta','cit_estado_asistencia','cit_estado_pago','cit_estado_confirmacion','cit_tipo']);

    return response()->json(['status'=>'ok','mensaje'=>'exito','cita'=>$cita],200); 
        
    }

/**
 * @api {put} /citas/:cit_id modifica el campo estado_asistencia de una cita. 
 * @apiVersion 0.2.0
 * @apiName PutCita
 
 * @apiParam {Number} cit_id Cita unique ID.
 * @apiSuccess {Array} cita
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra una cita con ese código.'
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "CitaNotFound"
 *     }
 */ 
    public function update(Request $request, $cit_id)
    {
         $cita=\awebss\Models\Cita::find($cit_id);

          if (!$cita)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una cita con ese código.'])],404);
        }

        $input = $request->all();

        $cita->update($input);

        return response()->json(['status'=>'ok','mensaje'=>'exito','cita'=>$cita],200);  
    }
/**
 * @api {put} /citas_confirmacion/:cit_id modifica el campo estado_confirmación.
 * @apiVersion 0.2.0
 * @apiName PutCitaConfirmacion
 
 * @apiParam {Number} cit_id Cita unique ID.
 * @apiSuccess {Array} cita
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra una cita con ese código.'
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "CitaNotFound"
 *     }
 */

    public function confirmar_cita(Request $request, $cit_id)
    {
         $cita=\awebss\Models\Cita::find($cit_id);

          if (!$cita)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una cita con ese código.'])],404);
        }

        $ad_id=$cita->ad_id;
        $atiende=\awebss\Models\Atiende_diariamente::find($ad_id);
        $fecha=$atiende->ad_fecha_atiende;

         $fecha_valida=Carbon::now()->addDay(1);

         if($fecha_valida<$fecha)
         {

        $input = $request->all();
        $cita->update($input);

        $atiende->ad_estado='OCUPADO';
        $atiende->save();
        $estado='true';
         }
        else
        {
            $estado='false';
        }

        return response()->json(['status'=>'ok','mensaje'=>'exito','cita'=>$estado],200);  
    }

/**
 * @api {delete} /citas/:cit_id Elimina una cita
 * @apiVersion 0.2.0
 * @apiName DeleteCita
 *
 * @apiParam {Number} cit_id Cita unique ID.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": registros eliminados correctamente
 *     }
 *
 * @apiError No se encuentra una cita con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "CitaNotFound"
 *     }
 */
public function destroy($cit_id)
    {
        $cita =\awebss\Models\Cita::find($cit_id);

 if (!$cita)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una cita con ese código.'])],404);
        }
$ad_id=$cita->ad_id;

$atiende=\awebss\Models\Atiende_diariamente::find($ad_id);
$atiende->ad_estado='DISPONIBLE';
$atiende->save();

        $cita->delete();

 return response()->json([
            "mensaje" => "registros eliminado correctamente"
            ], 200
        );
        
    }

}
