<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use awebss\Models\Paciente_establecimiento;

class Paciente_establecimientoController extends Controller
{
public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store']]);
    }
/**
 * @api {post}/pacientes_establecimientos permite admitir a un paciente que ya esta en el sistema como paciente.
 * @apiVersion 0.2.0
 * @apiName PostPaciente
 * @apiSuccess {Array} paciente_establecimiento
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

        $pac_id=$request->pac_id;
        $pe_hist_clinico=$request->pe_hist_clinico;
        $es_id=$request->es_id;
        $paciente_establecimiento=Paciente_establecimiento::desactivar_paciente($pac_id);

        if($pe_hist_clinico!=null)
        {
        $paciente_establecimiento = new Paciente_establecimiento();
        $paciente_establecimiento->es_id=$es_id;
        $paciente_establecimiento->pac_id=$pac_id;
        $paciente_establecimiento->pe_hist_clinico=$pe_hist_clinico;
        $paciente_establecimiento->pe_estado='ACTIVO';
        $paciente_establecimiento->userid_at=JWTAuth::toUser()->id;
        $paciente_establecimiento->save(); 

        return response()->json(['status'=>'ok','paciente_establecimiento'=>$paciente_establecimiento],200);  
        }

          $paciente_es= Paciente_establecimiento::activar_paciente($pac_id,$es_id);
 
        return response()->json(['status'=>'ok','paciente_establecimiento'=>$paciente_es],200);
    }

/**
 * @api {get} /pacientes_establecimientos Obtiene los establecimientos a los que pertenece un paciente
 * @apiVersion 0.2.0
 * @apiName GetPacienteEstablecimiento
 * @apiParam {Number} pac_id ID del paciente
 * @apiSuccess {array} paciente_establecimiento
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError paciente_establecimiento Array[]
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "PacienteNotFound"
 *     }
 */ 
    public function show($pac_id)
    {

    $paciente_establecimiento=Paciente_establecimiento::where('pac_id',$pac_id)->join('establecimiento_salud','establecimiento_salud.es_id','=','paciente_establecimiento.es_id')->select('establecimiento_salud.es_id','es_nombre','pac_id','paciente_establecimiento.pe_hist_clinico','pe_estado')->get();

        return response()->json(['status'=>'ok','paciente'=>$paciente_establecimiento],200); 
    }
/**
 * @api {get} /establecimientos_reservas Obtiene los establecimientos en los que un paciente puede realizar la reserva
 * @apiVersion 0.2.0
 * @apiName GetPacienteReserva
 * @apiParam {Number} pac_id ID del paciente
 * @apiSuccess {array} establecimiento_s
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError establecimiento_s Array[]
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "EstablecimientoNotFound"
 *     }
 */ 
      public function listar_establecimientos_habilitados($pac_id)
    {
    $c=0;
    $referencia_establecimiento=\awebss\Models\Boleta_referencia::where('pac_id',$pac_id)->get();
    foreach ($referencia_establecimiento as $referencia) {
       $actual=Carbon::now();
       $fecha=$referencia->created_at;
       $vigente=$fecha->addDays(5);
  if($actual<=$vigente)
    {
        $c=1;
        $br_id=$referencia->br_id;
    }  
    }
    if($c==1)
    {
    $estado=1;

    $referencia=\awebss\Models\Boleta_referencia::find($br_id);
    $es_id=$referencia->es_id_destino;

    $establecimiento_salud=\awebss\Models\Establecimiento_salud::where('es_id',$es_id)->join('boleta_referencia','boleta_referencia.es_id_destino','=','establecimiento_salud.es_id')->where('boleta_referencia.pac_id',$pac_id)->get(['establecimiento_salud.es_nombre','establecimiento_salud.es_id']);}
    $estado=0;
    $paciente_establecimiento=Paciente_establecimiento::where('pac_id',$pac_id)->join('establecimiento_salud','establecimiento_salud.es_id','=','paciente_establecimiento.es_id')->where('establecimiento_salud.es_nivel','PRIMER NIVEL')->get(['establecimiento_salud.es_nombre','establecimiento_salud.es_id']);
      $co=0;
      foreach ($paciente_establecimiento as $paciente) {
        if($c==1){
    foreach ($establecimiento_salud as $establecimiento) {
        $es_nombre=$establecimiento->es_nombre;
        $es_id=$establecimiento->es_id;
        $establecimiento_s[$co]=['es_nombre'=>$es_nombre,'es_id'=>$es_id];
        $co++; } }
         $es_nombre=$paciente->es_nombre;
         $es_id=$paciente->es_id;
        $establecimiento_s[$co]=['es_nombre'=>$es_nombre,'es_id'=>$es_id]; 
        $co++;        
      }  
return response()->json(['status'=>'ok','establecimiento'=>$establecimiento_s],200);  }

/**
 * @api {get} /pacientes_referencias Obtiene las referencias de un paciente // no se usa
 * @apiVersion 0.2.0
 * @apiName GetPacienteReserva
 * @apiParam {Number} pac_id ID del paciente
 * @apiSuccess {array} establecimiento_s
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 * @apiError establecimiento_s Array[]
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "EstablecimientoNotFound"
 *     }
 */ 
    public function listar_referencia_paciente($pac_id)
    {   
        $referencia_paciente=\awebss\Models\Boleta_referencia::where('pac_id',$pac_id)->get();
        
         return response()->json(['status'=>'ok','referencia'=>$referencia_paciente],200);
    }
/**
 * @api {get} /pacientes_establecimientos Verifica que un paciente pertenezca a un establecimiento de salud
 * @apiVersion 0.2.0
 * @apiName GetPacienteReserva
 * @apiParam {Number} pac_id ID del paciente
 * @apiSuccess {bool} true/false 
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 * @apiError false
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "Paciente_establecimientoNotFound"
 *     }
 */ 
public function buscar_paciente_establecimiento($pac_id, $es_id)
    {   
        $paciente_establecimiento=Paciente_establecimiento::where('pac_id',$pac_id)->get();

        $estado='false';

        foreach($paciente_establecimiento as $paciente) {  

        $es_id_p=$paciente->es_id;

        if($es_id_p==$es_id)
        {
            $estado='true'; } }
        
        return response()->json(['status'=>'ok','estado'=>$estado],200); 
    }
   
}
