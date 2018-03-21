<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Validator;
use JWTAuth;
use Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;
use awebss\Models\Detalle_vacuna;
use awebss\Models\Persona;
use awebss\Models\Dosis_vacuna;
use awebss\Models\Paciente;

class Detalle_vacunaController extends Controller
{
    public function __construct()
    {
       $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    } 

    /**
 * @api {post}/detalle_vacunas genera esquema de vacunacion para un paciente
 * @apiVersion 0.2.0
 * @apiName PostDetalle_Vacuna
 * @apiSuccess {Array} detalle_vacuna
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
        ]);

    if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }
    $pacientes=Paciente::find($request->pac_id);
    $personas=Persona::find($pacientes->per_id);
    

    $dosis_vacuna=Dosis_vacuna::all();

    foreach ($dosis_vacuna as $dosis) 
    {      
            $fecha=new Carbon($personas->per_fecha_nacimiento);
        
            $vac_detalle = new Detalle_vacuna();
            $vac_detalle->userid_at=JWTAuth::toUser()->id;
            $vac_detalle->dov_id=$dosis->dov_id;
            $vac_detalle->pac_id=$request->pac_id;
            $vac_detalle->dev_fecha_estimada= $fecha->addMonth($dosis->dov_edad_inicio);
            $vac_detalle->dev_observacion = 'NINGUNO';
            $vac_detalle->dev_estado_aplicado=False; 
            $vac_detalle->dev_tipo_servicio='Dentro de servicio';
            $vac_detalle->dev_edad_aplicacion=0;
            $vac_detalle->save();       }

$detalle_vacuna=Detalle_vacuna::where('pac_id',$request->pac_id)->get();

return response()->json([
                'status'=>'ok',"msg" => "exito",
          "detalle_vacuna" => $detalle_vacuna
            ], 200
        ); 
    }

/**
 * @api {get} /detalle_vacunas Obtiene el esquema de vacunacion de un paciente
 * @apiVersion 0.2.0
 * @apiName GetDetalle_vacuna
 * @apiParam {Number} pac_id ID del paciente
 * @apiSuccess {array} detalle_vacuna
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentraron registros.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "DetalleVacunaNotFound"
 *     }
 */ 
    public function show(Request $request, $pac_id)
    {
$detalle_vacuna=Detalle_vacuna::where('pac_id',$pac_id)->join('_dosis_vacuna','_dosis_vacuna.dov_id','=','detalle_vacuna.dov_id')->join('_vacuna','_vacuna.vac_id','=','_dosis_vacuna.vac_id')->select('_vacuna','_vacuna.vac_id','=','_dosis_vacuna.vac_id')->select('_vacuna.vac_id','_dosis_vacuna.dov_id','detalle_vacuna.dev_id','dov_numero_dosis','dov_tipo','vac_nombre','dev_tipo_servicio','dev_observacion','dev_fecha_aplicada','dev_estado_aplicado','dov_suministro','dov_edad_inicio','dev_fecha_estimada','dev_edad_aplicacion')->orderBy('_dosis_vacuna.dov_edad_inicio')->get();

if(count($detalle_vacuna)<=0)
{
    return response()->json([
                'status'=>'ok',"msg" => "exito",
          "detalle_vacuna" => 'no se encontraron registros'
            ], 200
        ); 
}
return response()->json([
                'status'=>'ok',"msg" => "exito",
          "detalle_vacuna" => $detalle_vacuna
            ], 200
        ); 
    }
/**
 * @api {put} /detalle_vacunas/:dev_id modifica los campos de detalle vacuna
 * @apiVersion 0.2.0
 * @apiName PutDetalleVacuna
 
 * @apiParam {Number} dev_id Detalle vacuna unique ID.
 * @apiSuccess {Array} detalle_vacuna
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     { *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra un detalle de vacuna con ese código.'
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "DetalleVacunaNotFound"
 *     }
 */ 
    public function update(Request $request, $dev_id)
    {
        $vac_detalle=Detalle_vacuna::find($dev_id);
       
         if (!$vac_detalle)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un detalle vacuna con ese código.'])],404);
        }

         $paciente=Paciente::find($vac_detalle->pac_id);
        $persona=Persona::find($paciente->per_id);

           $fecha_aplicada=new \Carbon\Carbon($request->dev_fecha_aplicada);
        
            $vac_detalle->dev_fecha_aplicada = $request->dev_fecha_aplicada;
            $vac_detalle->dev_observacion = $request->dev_observacion;
            $vac_detalle->dev_estado_aplicado=$request->dev_estado_aplicado; 
            $vac_detalle->dev_tipo_servicio=$request->dev_tipo_servicio;
            $vac_detalle->dev_edad_aplicacion=date_diff($fecha_aplicada,$persona->per_fecha_nacimiento)->y*12 + date_diff($fecha_aplicada,$persona->per_fecha_nacimiento)->m;
            $vac_detalle->save();
        
        return response()->json([
                'status'=>'ok',"msg" => "exito",
          "detalle_vacuna" => $vac_detalle
            ], 200);

    }
/**
 * @api {delete} /detalle_vacunas/:dev_id Elimina un detalle de vacuna
 * @apiVersion 0.2.0
 * @apiName DeleteDetalleVacuna
 *
 * @apiParam {Number} dev_id Detalle_vacuna unique ID.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": registros eliminados correctamente
 *     }
 *
 * @apiError No se encuentra un detalle vacuna con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "DetalleVacunaNotFound"
 *     }
 */

    public function destroy($pac_id)
    {
        $vac_detalle=Detalle_vacuna::where('pac_id',$pac_id)->get();
       
         if (!$vac_detalle)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un detalle vacuna con ese código.'])],404);
        }

        foreach ($vac_detalle as $detalle) {

        	$detalle->delete();
        	# code...
        }

        return response()->json([
                'status'=>'ok',"msg" => "registros eliminados correctamente"
            ], 200
        ); 

    }
/**
 * @api {get} /detalle_vacunas Obtiene las vacunas aplicadas en un rango de fechas y rango de edades
 * @apiVersion 0.2.0
 * @apiName GetDetalle_vacuna
 * @apiSuccess {array} detalle_vacuna
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra consultoriosen ese establecimiento.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "DetalleVacunaNotFound"
 *     }
 */
     public function index(Request $request)
    { 

        $validator = Validator::make($request->all(), [
            
            'fecha1' => 'required',
            'fecha' => 'required',
            'edad'=>'required',
            'edad1'=>'required',
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }

        $dosis=Dosis_vacuna::join('_vacuna','_vacuna.vac_id','=','_dosis_vacuna.vac_id')->where('_dosis_vacuna.dov_edad_inicio','<',$request->edad1)->where('_dosis_vacuna.dov_edad_fin','>=',$request->edad)->get(['_vacuna.vac_nombre','dov_numero_dosis','_dosis_vacuna.dov_id']);
        if(count($dosis)<=0)
        {
            return response()->json([
                'status'=>'ok',"mensaje" => "no se encuentran dosis de vacunas con esos datos"
            ], 200
        ); 

        }
        
        foreach ($dosis as $dosis) {
            
       $detalle1=Detalle_vacuna::join('paciente','paciente.pac_id','=','detalle_vacuna.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->where('detalle_vacuna.dev_fecha_aplicada','>=',$request->fecha)->where('detalle_vacuna.dev_fecha_aplicada','<=',$request->fecha1)->where('persona.per_genero','M')->where('detalle_vacuna.dev_tipo_servicio','DENTRO')->where('detalle_vacuna.dov_id',$dosis->dov_id)->where('detalle_vacuna.dev_edad_aplicacion','>=',$request->edad)->where('detalle_vacuna.dev_edad_aplicacion','<',$request->edad1)->get(['paciente.pac_id']); 
        
        $detalle2=Detalle_vacuna::join('paciente','paciente.pac_id','=','detalle_vacuna.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->where('detalle_vacuna.dev_fecha_aplicada','>=',$request->fecha)->where('detalle_vacuna.dev_fecha_aplicada','<=',$request->fecha1)->where('persona.per_genero','F')->where('detalle_vacuna.dev_tipo_servicio','DENTRO')->where('detalle_vacuna.dov_id',$dosis->dov_id)->where('detalle_vacuna.dev_edad_aplicacion','>=',$request->edad)->where('detalle_vacuna.dev_edad_aplicacion','<',$request->edad1)->get(['paciente.pac_id']);
        
       $detalle3=Detalle_vacuna::join('paciente','paciente.pac_id','=','detalle_vacuna.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->where('detalle_vacuna.dev_fecha_aplicada','>=',$request->fecha)->where('detalle_vacuna.dev_fecha_aplicada','<=',$request->fecha1)->where('persona.per_genero','M')->where('detalle_vacuna.dev_tipo_servicio','FUERA')->where('detalle_vacuna.dov_id',$dosis->dov_id)->where('detalle_vacuna.dev_edad_aplicacion','>=',$request->edad)->where('detalle_vacuna.dev_edad_aplicacion','<',$request->edad1)->get(['paciente.pac_id']);
        

        $detalle4=Detalle_vacuna::join('paciente','paciente.pac_id','=','detalle_vacuna.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->where('detalle_vacuna.dev_fecha_aplicada','>=',$request->fecha)->where('detalle_vacuna.dev_fecha_aplicada','<=',$request->fecha1)->where('persona.per_genero','F')->where('detalle_vacuna.dev_tipo_servicio','FUERA')->where('detalle_vacuna.dov_id',$dosis->dov_id)->where('detalle_vacuna.dev_edad_aplicacion','>=',$request->edad)->where('detalle_vacuna.dev_edad_aplicacion','<',$request->edad1)->get(['paciente.pac_id']); 
        
        $array[]=['edades'=>$request->edad.'-'.$request->edad1, 'vacuna'=>$dosis->vac_nombre,'nro_dosis'=>$dosis->dov_numero_dosis,'detalle1'=>count($detalle1),'detalle2'=>count($detalle2),'detalle3'=>count($detalle3),'detalle4'=>count($detalle4)];

        } 

        return response()->json([
                'status'=>'ok',"msg" => "exito",
          "detalle_vacuna" => $array
            ], 200
        ); 
    }


}
