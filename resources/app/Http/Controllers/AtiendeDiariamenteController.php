<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AtiendeDiariamenteController extends Controller
{

public function __construct()
    {
    $this->middleware('jwt.auth',['only' => ['store','update','generar_atiendes']]);
    } 

    /**
 * @api {get} /atiende_diariamente Obtiene las configuracions de horario de un funcionario
 * @apiVersion 0.2.0
 * @apiName GetConfiguracionFuncionario
 conforme todo ha ido bien.
 * @apiSuccess {array} atiende_diariamente[]
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "code": 0,
 *       "response": true
 *     }
 *
 * @apiError .
 */
    public function index(Request $request)
    {

    $fe_id=$request->fe_id;

    $fecha=Carbon::now();
    $turno=$request->ct_turno;

$atiende=\awebss\Models\Configuracion_horario::where('fe_id',$fe_id)->join('configuracion_turno','configuracion_turno.ch_id','=','configuracion_horario.ch_id')->join('atiende_diariamente','atiende_diariamente.ct_id','=','configuracion_turno.ct_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','configuracion_horario.sc_id')->join('consultorio','consultorio.con_id','=','servicio_consultorio.con_id')->where('atiende_diariamente.ad_estado','DISPONIBLE')->where('configuracion_turno.ct_turno',$turno)->where('atiende_diariamente.ad_fecha_atiende',$fecha)->select('servicio_consultorio.sc_id','consultorio.con_id','con_nombre','atiende_diariamente.ad_id','ad_fecha_atiende','ad_hora_inicio','ad_numero_ficha','ad_estado','configuracion_turno.ct_id','ct_turno')->get(); 

   return response()->json(['status'=>'ok','atiende_diariamente'=>$atiende],200); 
 
    }
 /**
 * @api {get} /reservas_atiende Obtiene los horarios que existen para un servicio
 * @apiVersion 0.2.0
 * @apiName GetHorariosServicios
 * @apiParam {Number} se_id id del servicio establacimiento
 * @apiSuccess {array} false/atiende_diariamente
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "code": 0,
 *       "response": true
 *     }
 *
 * @apiError No se encuentra un servicio con ese código.
 */
    public function listar_horarios_diariamente(Request $request, $se_id)
    {

  $fecha=Carbon::now()->addDay(1);

  $servicio_establecimiento=\awebss\Models\Servicio_establecimiento::find($se_id);

    if (!$servicio_establecimiento)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un servicio con ese código.'])],404);
        }

    $pac_id=$request->pac_id;

    $paciente=\awebss\Models\Cita::where('cit_fecha_consulta',$fecha)->where('pac_id',$pac_id)->get();
    $count= count($paciente);

    if($count<=0)
    {

      $servicio_consultorio=\awebss\Models\Servicio_consultorio::where('se_id',$se_id)->join('consultorio','consultorio.con_id','=','servicio_consultorio.con_id')->join('configuracion_horario','configuracion_horario.sc_id','=','servicio_consultorio.sc_id')->join('configuracion_turno','configuracion_turno.ch_id','=','configuracion_horario.ch_id')->join('atiende_diariamente','atiende_diariamente.ct_id','=','configuracion_turno.ct_id')->where('atiende_diariamente.ad_estado','DISPONIBLE')->where('atiende_diariamente.ad_fecha_atiende',$fecha)->select('servicio_consultorio.sc_id','consultorio.con_id','con_nombre','atiende_diariamente.ad_id','ad_fecha_atiende','ad_hora_inicio','ad_numero_ficha','ad_estado','configuracion_turno.ct_id','ct_turno','ct_ficha_sesar','ct_ficha_total')->get();   

   return response()->json(['status'=>'ok','atiende_diariamente'=>$servicio_consultorio],200); 
    }

    else
    {

    return response()->json(['status'=>'ok','atiende_diariamente'=>'false'],200); 

    }

}
 /**
 * @api {post} /atiende_diariamente Cancela horarios de un establecimiento de salud para un dia
 * @apiVersion 0.2.0
 * @apiName PostAtiendeDiariamente
 * @apiSuccess {Bool} atiende_diariamente[]
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "code": 0,
 *       "response": true
 *     }
 *
 * @apiError BadResponse
 *
 * @apiError BaD-Response:
 *     HTTP/1.1 400 Bad Response
 *     {
 *       "error": "BadResponse"
 *     }
 */
public function store(Request $request)
    {

$fecha=$request->fecha;
$es_id=$request->es_id;

$fecha_valida=Carbon::now()->addDay(1);
   
$establecimiento_atiende=\awebss\Models\Configuracion_horario::join('configuracion_turno','configuracion_turno.ch_id','=','configuracion_horario.ch_id')->join('atiende_diariamente','atiende_diariamente.ct_id','=','configuracion_turno.ct_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','configuracion_horario.sc_id')->join('funcionario_establecimiento','funcionario_establecimiento.fe_id','=','configuracion_horario.fe_id')->where('atiende_diariamente.ad_fecha_atiende',$fecha)->where('funcionario_establecimiento.es_id',$es_id)->get(['configuracion_horario.ch_id','atiende_diariamente.ad_id','ad_estado','ad_fecha_atiende']);

$count= count($establecimiento_atiende);

if($count<=0)

{

return response()->json(['status'=>'ok','mensaje'=>'exito','atiende_diariamente'=>$establecimiento_atiende],200);  

}

else {

     if($fecha_valida<$fecha)

    {
         foreach ($establecimiento_atiende as $atiende) {

            $ad_id=$atiende->ad_id;

            $atiende_diariamente=\awebss\Models\Atiende_diariamente::find($ad_id);
            $atiende_diariamente->ad_estado='OCUPADO';
            $atiende_diariamente->save();     }

            $estado='true';
    }
    else { $estado='false';} 

    return response()->json(['status'=>'ok','mensaje'=>'exito','atiende_diariamente'=>$estado],200);

}
    }

/**
 * @api {get} /atiende_diariamente Obtiene los atiende de una configuracion de horario dado una fecha para la cita programada
 * @apiVersion 0.2.0
 * @apiName GetAtiendeFecha
 * @apiParam {Number} fe_id ID del funcionario
 * @apiSuccess {Array} funcionario_atiende/false
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "code": 0,
 *       "response": true
 *     }
 *
 * @apiError 
 */
    
   public function show(Request $request, $fe_id)
{
    $fecha=$request->fecha;

    $pac_id=$request->pac_id;

    $paciente=\awebss\Models\Cita::where('cit_fecha_consulta',$fecha)->where('pac_id',$pac_id)->get();
    $count= count($paciente);

    if($count==0)
    {

$cita=\awebss\Models\Configuracion_horario::select('atiende_diariamente.ad_id')->join('configuracion_turno','configuracion_turno.ch_id','=','configuracion_horario.ch_id')->join('atiende_diariamente','atiende_diariamente.ct_id','=','configuracion_turno.ct_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','configuracion_horario.sc_id')->where('configuracion_horario.fe_id',$fe_id)->where('atiende_diariamente.ad_fecha_atiende',$fecha)->where('atiende_diariamente.ad_estado','DISPONIBLE')->join('cita','cita.ad_id','=','atiende_diariamente.ad_id')->get();

$funcionario_atiende=\awebss\Models\Configuracion_horario::select('atiende_diariamente.ad_id','ad_fecha_atiende','ad_hora_inicio','ad_numero_ficha','ad_estado','configuracion_turno.ct_id','ct_dia','ct_turno','ct_ini_turno','ct_fin_turno','ct_ficha_total','ct_ficha_sesar','ct_promedio','servicio_consultorio.sc_id','configuracion_horario.fe_id','servicio_consultorio.con_id')->join('configuracion_turno','configuracion_turno.ch_id','=','configuracion_horario.ch_id')->join('atiende_diariamente','atiende_diariamente.ct_id','=','configuracion_turno.ct_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','configuracion_horario.sc_id')->where('configuracion_horario.fe_id',$fe_id)->where('atiende_diariamente.ad_fecha_atiende',$fecha)->where('atiende_diariamente.ad_estado','DISPONIBLE')->whereNotIn('atiende_diariamente.ad_id',$cita)->get();  

    return response()->json(['status'=>'ok',"msg" => "exito",'funcionario_atiende'=>$funcionario_atiende],200);    
    }

    else
    {

    return response()->json(['status'=>'ok',"msg" => "exito",'funcionario_atiende'=>'false'],200);  

    }   
}

 /**
 * @api {get} /horarios_atiende_diariamente Crea información para los atiendes de una configuracion de horario
 * @apiVersion 0.2.0
 * @apiName GetHorariosAtiende
 * @apiParam {Number} ch_id ID de la configuración de horario.
 * @apiSuccess {Number} code  Código 0 conforme todo ha ido bien.
 * @apiSuccess {Array} atiende_diariamente/false.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "code": 0,
 *       "response": atiende_diariamente
 *     }
 *
 * @apiError BadResponse
 *
 * @apiError BaD-Response:
 *     HTTP/1.1 400 Bad Response
 *     {
 *       "error": "BadResponse"
 *     }
 */
public function generar_atiendes($ch_id)

{
     $configuracion_horario=\awebss\Models\Configuracion_horario::find($ch_id);

      if (!$configuracion_horario)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una configuacion horario con ese código.'])],404);
        }

        $ch_fecha_inicio=$configuracion_horario->ch_fecha_inicio;
        $ch_fecha_final=$configuracion_horario->ch_fecha_final;
        $fechaInicio=strtotime($ch_fecha_inicio);
        $fechaFin=strtotime($ch_fecha_final);
        $configuracion_turno=\awebss\Models\Configuracion_turno::where('ch_id',$ch_id)->get();
       
        foreach($configuracion_turno as $turno) 
    {  
        $ct_id=$turno->ct_id;
        $ct_dia=$turno->ct_dia;
        $ct_ini_turno=$turno->ct_ini_turno;
        $ct_ini_turno=strtotime($ct_ini_turno);
        $ct_fin_turno=$turno->ct_fin_turno;
        $ct_promedio=$turno->ct_promedio;
        $ct_fin_turno= strtotime($ct_fin_turno);
        $ct_ficha_total=$turno->ct_ficha_total;

        $c=0;
        $fechas='';
         //$fechas = array(['fecha' => '']);
    //Recorro las fechas y con la función strotime obtengo los lunes
    for($i=$fechaInicio; $i<=$fechaFin; $i+=86400)
        {
    //Sacar el dia de la semana con el modificador N de la funcion date
    $dia = date('N', $i);
    if($dia==$ct_dia)
            {
                $date=date ("Y-m-d", $i);
                //$fechas=$date;
               $fechas[]= $date;
            }
                $c=$c+1;
        }  
    $longitud=sizeof($fechas);

   for($i=0;  $i< $longitud ;$i++)
    {   
        $rezagados=30*60;
        $añadir=(($ct_promedio*$ct_ficha_total)*60)+$rezagados;
        $minutos_añadir=$ct_promedio*60;
        $minutos_total=$minutos_añadir+$rezagados;
        $ini_sesar=$ct_fin_turno-$añadir;
        $fin_sesar=$ct_fin_turno-$minutos_total;
        $cont=1;

        if($ini_sesar==$ct_ini_turno || $ini_sesar>$ct_ini_turno)
        {

   for( $y=$ini_sesar ;$y<=$fin_sesar; $y=$y+$minutos_añadir)
        {

        $atiende_diariamente = new \awebss\Models\Atiende_diariamente();
        $atiende_diariamente->ad_fecha_atiende=$fechas[$i];
        $hora_atiende=date('H:i',$y);
        $atiende_diariamente->ad_hora_inicio=$hora_atiende;
        $atiende_diariamente->ad_numero_ficha=$cont;
        $atiende_diariamente->ad_estado='DISPONIBLE';
        $atiende_diariamente->ct_id=$ct_id;
        $atiende_diariamente->userid_at=JWTAuth::toUser()->id;
        $cont=$cont+1;
        $atiende_diariamente->save();
        } 
    }
    else {return response()->json(['status'=>'ok','mensaje'=>'exito','atiende_diariamente'=>'false'],200);}

    }  

}
$configuracion_horario=\awebss\Models\Configuracion_turno::where('ch_id',$ch_id)->join('atiende_diariamente','atiende_diariamente.ct_id','=','configuracion_turno.ct_id')->select('atiende_diariamente.ad_id','configuracion_turno.ct_id','ad_fecha_atiende','ad_hora_inicio','ad_numero_ficha','ad_estado')->get();

    return response()->json(['status'=>'ok','mensaje'=>'exito','atiende_diariamente'=>$configuracion_horario],200);
}


// ACTUALIZAR cancela horarios de un medico


/**
 * @api {put} /atiende_diariamente/:fe_id cancela horarios de un medico 
 * @apiVersion 0.2.0
 * @apiName PutAtiendeDiariamente
 
 * @apiParam {Number} fe_id funcionario establecimiento unique ID.
 * @apiSuccess {Bool} false/atiende_diariamente[].
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "code": 0,
 *       "response": true
 *     }
 *
 * @apiError 
 */ 
     public function update(Request $request, $fe_id)
    {  

    $fecha=$request->fecha;

    $fecha_valida=Carbon::now()->addDay(1);
   
    $funcionario_atiende=\awebss\Models\Configuracion_horario::join('configuracion_turno','configuracion_turno.ch_id','=','configuracion_horario.ch_id')->join('atiende_diariamente','atiende_diariamente.ct_id','=','configuracion_turno.ct_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','configuracion_horario.sc_id')->where('configuracion_horario.fe_id',$fe_id)->where('atiende_diariamente.ad_fecha_atiende',$fecha)->get(['atiende_diariamente.ad_id','ad_estado','ad_fecha_atiende']);

$count= count($funcionario_atiende);

if($count<=0)

{

return response()->json(['status'=>'ok','mensaje'=>'exito','atiende_diariamente'=>$funcionario_atiende],200);  

}

else {

 if($fecha_valida<$fecha)

    {
         foreach ($funcionario_atiende as $atiende) {
            # code...

            $ad_id=$atiende->ad_id;

            $atiende_diariamente=\awebss\Models\Atiende_diariamente::find($ad_id);
            $atiende_diariamente->ad_estado='OCUPADO';
            $atiende_diariamente->save();     }

            $estado='true';
    }
    else { $estado='false';}
    return response()->json(['status'=>'ok','mensaje'=>'exito','atiende_diariamente'=>$estado],200);

}

} 

}
