<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use Carbon;

class Configuracion_turnoController extends Controller
{
     public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','destroy']]);
    }
//probar
public function index($con_id)
    {

    $validator = Validator::make($request->all(), [
            
            'ch_fecha_inicio' => 'required',
            'ch_fecha_final'=>'required',    
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }

   //$fecha_actual=Carbon::now();

    $configuracion_horario=\awebss\Models\Configuracion_horario::select('configuracion_turno.ct_id','ct_dia','ct_turno','ct_ini_turno','ct_fin_turno','ct_ficha_total','ct_ficha_sesar','configuracion_horario.ch_id','persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','servicio_consultorio.sc_id','servicio.ser_id','ser_nombre')->join('configuracion_turno','configuracion_turno.ch_id','=','configuracion_horario.ch_id')->join('funcionario_establecimiento','funcionario_establecimiento.fe_id','=','configuracion_horario.fe_id')->join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','configuracion_horario.sc_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->where('servicio_consultorio.con_id',$con_id)->where('configuracion_horario.ch_fecha_final','<=',$request->ch_fecha_final)->where('configuracion_horario.ch_fecha_inicio','>=',$request->ch_fecha_inicio)->get();

     $count=count($configuracion_horario);

    if($count<=0)
    {

    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentran turnos para ese consultorio.'])],404);

    }
        return response()->json(['status'=>'ok','mensaje'=>'exito','configuracion_turno'=>$configuracion_horario],200);
    }


 /**
 * @api {post}/configuracion_turnos Crea información para una configuracion de turno.
 * @apiVersion 0.2.0
 * @apiName PostConfiguracionTurno
 * @apiSuccess {Number} code  Código 0 conforme todo ha ido bien.
 * @apiSuccess {Array} configuracion_turno.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "code": 0,
 *       "response": configuracion_turno
 *     }
 *
 * @apiError Configuracion_turnoBadResponse La configuración de turno no ha podido crearse
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
            
            'ch_id' => 'required',
            'ct_dia'=>'required',
            'ct_turno'=>'required',
            'ct_ini_turno'=>'required',
            'ct_fin_turno'=>'required',
            'ct_ficha_total'=>'required',
            
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }

        
        $ct_ini_turno=$request->ct_ini_turno;
        $ct_ini_turno=strtotime($ct_ini_turno);
        $ct_promedio=15;
        $ct_fin_turno=$request->ct_fin_turno;
        $ct_fin_turno= strtotime($ct_fin_turno);
        $ct_ficha_total=$request->ct_ficha_total;

        $rezagados=30*60;
        $añadir=(($ct_promedio*$ct_ficha_total)*60)+$rezagados;
        $minutos_añadir=$ct_promedio*60;   
        $minutos_total=$minutos_añadir+$rezagados;
        $ini_sesar=$ct_fin_turno-$añadir;
        $fin_sesar=$ct_fin_turno-$minutos_total;

         if($ini_sesar==$ct_ini_turno || $ini_sesar>$ct_ini_turno)
        {
        $configuracion_turno= new \awebss\Models\Configuracion_turno();
        $configuracion_turno->ch_id=$request->ch_id;
        $configuracion_turno->ct_dia=$request->ct_dia;
        $configuracion_turno->ct_turno=$request->ct_turno;
        $configuracion_turno->ct_ini_turno=$request->ct_ini_turno;
        $configuracion_turno->ct_fin_turno=$request->ct_fin_turno;
        $configuracion_turno->ct_promedio=15;
        $configuracion_turno->ct_ficha_total=$request->ct_ficha_total;
        $configuracion_turno->ct_ficha_sesar=$request->ct_ficha_sesar;
        $configuracion_turno->userid_at=JWTAuth::toUser()->id;
        $configuracion_turno->save(); 
       }

       else
       {

        return response()->json(['status'=>'ok','mensaje'=>'exito','configuracion_turno'=>'false'],200); 
       }

        return response()->json(['status'=>'ok','mensaje'=>'exito','configuracion_turno'=>$configuracion_turno],200); 
    }

 /**
 * @api {delete} /configuracion_turnos/:ct_id Elimina una configuracion de turno en cascada
 * @apiVersion 0.2.0
 * @apiName DeleteConfiguraciónTurno
 *
 * @apiParam {Number} ct_id Configuracion_turno unique ID.
 *
 * @apiSuccess {Number} code  Código 0 conforme todo ha ido bien.
 * @apiSuccess {Bool} true/false True o false dependiendo del resultado.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "code": 0,
 *       "response": true
 *     }
 *
 * @apiError No se encuentra una configuracion de turno con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "Configuracion_turnoNotFound"
 *     }
 */

    public function destroy($ct_id)
    {
        $configuracion_turno = \awebss\Models\Configuracion_turno::find($ct_id);

          if (!$configuracion_turno)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un turno con ese código.'])],404);
        }

        $atiende_diariamente=\awebss\Models\Atiende_diariamente::where('ct_id',$ct_id)->get();

        foreach ($atiende_diariamente as $atiende) {
            $ad_id=$atiende->ad_id;

            $atiende_diariamente=\awebss\Models\Atiende_diariamente::find($ad_id);

            $atiende_diariamente->delete();
        }

         $configuracion_turno->delete();
         
    return response()->json([ "mensaje" => "registros eliminados correctamente turno"], 200);
    
    }
}
