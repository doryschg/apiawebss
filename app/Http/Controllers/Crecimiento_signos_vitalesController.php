<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;
use Validator;
use JWTAuth;
use Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;
use awebss\Models\Crecimiento_signos_vitales;
use awebss\Models\Persona;
use awebss\Models\Dosis_vacuna;
use awebss\Models\Paciente;

class Crecimiento_signos_vitalesController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update']]);
    }
 /**
 * @api {post}/crecimientos crea registros de control de crecimiento y signos vitales
 * @apiVersion 0.2.0
 * @apiName PostCrecimiento
 * @apiSuccess {Array} crecimiento
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

            'pac_id' => 'required',
        ]);

    if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }
        $crecimiento= new Crecimiento_signos_vitales();
        $crecimiento->csv_talla=$request->csv_talla;
        $crecimiento->csv_peso=$request->csv_peso;
        $crecimiento->csv_peso_talla=$request->csv_peso_talla;
        $crecimiento->csv_imc_calculado=$request->csv_imc_calculado;
        $crecimiento->csv_talla_edad=$request->csv_talla_edad;
        $crecimiento->csv_temp=$request->csv_temp;
        $crecimiento->csv_fc=$request->csv_fc;
        $crecimiento->csv_pa=$request->csv_pa;
        $crecimiento->csv_fr=$request->csv_fr;
        $crecimiento->csv_fecha_control=$request->csv_fecha_control;
        $crecimiento->csv_observacion=$request->csv_observacion;
        $crecimiento->csv_edad_control=$request->csv_edad_control;
        $crecimiento->pac_id=$request->pac_id;
        $crecimiento->userid_at=JWTAuth::toUser()->id;
        $crecimiento->save();

        return response()->json(['status'=>'ok','mensaje'=>'exito','crecimiento'=>$crecimiento],200);
    }

/**
 * @api {get} /crecimientos Obtiene los registros de control de crecimiento de un paciente
 * @apiVersion 0.2.0
 * @apiName GetCrecimiento
 * @apiParam {Number} pac_id ID del paciente
 * @apiSuccess {array} crecimiento
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un control de crecimiento con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "CrecimientoNotFound"
 *     }
 */ 

    public function show(Request $request, $pac_id)
    {

        $validator = Validator::make($request->all(), [
            
            'nro' => 'required',
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }

        $crecimiento=Crecimiento_signos_vitales::where('pac_id',$pac_id)->select('crecimiento_signos_vitales.csv_id','csv_talla','csv_peso','csv_peso_talla','csv_imc_calculado','csv_talla_edad','csv_temp','csv_fc','csv_pa','csv_fr','csv_fecha_control','csv_observacion','csv_edad_control')->orderBy('csv_fecha_control','DESC')->paginate($request->nro);

        if(count($crecimiento)<=0)
        {
            return response()->json(['status'=>'ok','mensaje'=>'no existen regitros de control de crecimiento para ese paciente'],200);
        }
        return response()->json(['status'=>'ok','mensaje'=>'exito','crecimiento'=>$crecimiento],200);

    }

/**
 * @api {put} /crecimientos/:dev_id modifica los campos de crecimiento y signos vitales
 * @apiVersion 0.2.0
 * @apiName PutCrecimiento
 
 * @apiParam {Number} dev_id crecimiento_signos_vitales unique ID.
 * @apiSuccess {Array} cita
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra un crecimiento y signos vitales con ese código.'
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "DetalleVacunaNotFound"
 *     }
 */ 

    public function update(Request $request, $csv_id)
    {
         $crecimiento=Crecimiento_signos_vitales::find($csv_id);

        if(!$crecimiento)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un control de crecimiento y signos vitales.'])],404);
        }

        $input=$request->all();
        $crecimiento->update($input);
        return response()->json(['status'=>'ok','mensaje'=>'exito','crecimiento'=>$crecimiento],200);
    }
   
}
