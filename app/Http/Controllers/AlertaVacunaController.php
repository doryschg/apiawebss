<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;
use awebss\Http\Requests;
use Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AlertaVacunaController extends Controller
{
    
      public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }
    public function index()
    {
        $vacuna=\awebss\Models\Alerta_temprana_vacuna::select('atv_id','vac_nombre','vac_cant_dosis','vac_descripcion','_mensaje_alerta.men_id','men_encabezado','men_cuerpo','men_despedida','men_tipo')->join('_vacuna','_vacuna.vac_id','=','alertas_temprana_vacuna.vac_id')->join('_mensaje_alerta','_mensaje_alerta.men_id','=','alertas_temprana_vacuna.men_id')->get();

        return response()->json(['status'=>'ok','mensaje'=>'exito','vacuna'=>$vacuna],200); 
    }

/**
 * @api {post}/vacuna_alerta crea informacion para la alerta de una vacuna tambien crea el mensaje
 * @apiVersion 0.2.0
 * @apiName PostVacunaAlerta
 * @apiSuccess {Array} vacuna_alerta
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
             
            'vac_id' => 'required',]);

        if ($validator->fails()) 
        {
            return $validator->errors()->all();
        } 

        $vac_id=$request->vac_id;

        $vacuna=\awebss\Models\Vacuna::find($vac_id);
        $vac_nombre=$vacuna->vac_nombre;

        $men_tipo='Alerta de vacuna: '.$vac_nombre;

        $mensaje_alerta= new \awebss\Models\Mensaje_alerta();
        $mensaje_alerta->men_encabezado=$request->men_encabezado;
        $mensaje_alerta->men_cuerpo=$request->men_cuerpo;
        $mensaje_alerta->men_despedida=$request->men_despedida;
        $mensaje_alerta->men_tipo=$men_tipo;
        $mensaje_alerta->userid_at=JWTAuth::toUser()->id;
        $mensaje_alerta->save();

        $vacuna_alerta=new \awebss\Models\Alerta_temprana_vacuna();
        $vacuna_alerta->men_id=$mensaje_alerta->men_id;
        $vacuna_alerta->vac_id=$vac_id;
        $vacuna_alerta->userid_at=JWTAuth::toUser()->id;
        $vacuna_alerta->save();
        $resultado=compact('mensaje_alerta','vacuna_alerta');

        return response()->json(['status'=>'ok','mensaje'=>'exito','vacuna_alerta'=>$resultado],200);
         }

/**
 * @api {get} /vacuna_alerta Obtiene informacion de una alerta de vacuna
 * @apiVersion 0.2.0
 * @apiName GetVacunaAlerta
 * @apiParam {Number} atv_id ID de la vacuna_alerta
 * @apiSuccess {array} detalle_vacuna
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra una vacuna alerta con ese codigo
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "VacunaAlertaNotFound"
 *     }
 */ 

    public function show( $atv_id)
    {
        $vacuna_alerta=\awebss\Models\Alerta_temprana_vacuna::find($atv_id);

         if (!$vacuna_alerta)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una vacuna alerta con ese código.'])],404);
        }

        $men_id=$vacuna_alerta->men_id;
        $vac_id=$vacuna_alerta->vac_id;

        $vacuna=\awebss\Models\Vacuna::find($vac_id)->toArray();;

          if (!$vacuna)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una vacuna con ese código.'])],404);
        }

        $mensaje_alerta=\awebss\Models\Mensaje_alerta::find($men_id)->toArray();;

         if (!$mensaje_alerta)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un mensaje de alerta con ese código.'])],404);
        }

        $vacuna_alerta=$vacuna_alerta->toArray();

        $vacuna_alerta=array_collapse([$vacuna,$mensaje_alerta]);

    return response()->json(['status'=>'ok','mensaje'=>'exito','vacuna_alerta'=>$vacuna_alerta],200); 
    }
/**
 * @api {put} /vacuna_alerta/:atv_id modifica los campos de vacuna alerta
 * @apiVersion 0.2.0
 * @apiName PutVacunaAlerta
 
 * @apiParam {Number} atv_id vacuna_alerta unique ID.
 * @apiSuccess {Array} cita
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra un alerta de vacuna con ese código.'
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "VacunaAlertaNotFound"
 *     }
 */ 
    public function update(Request $request, $atv_id)
    {
        $vacuna_alerta=\awebss\Models\Alerta_temprana_vacuna::find($atv_id);

         if (!$vacuna_alerta)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una vacuna con ese código.'])],404);
        } 
        $men_id=$vacuna_alerta->men_id;

        $mensaje=\awebss\Models\Mensaje_alerta::find($men_id);

        $input = $request->all();
        $mensaje->update($input);

        return response()->json(['status'=>'ok',"msg" => "exito","vacuna_alerta" => $vacuna_alerta], 200);
    }

/**
 * @api {delete} /vacuna_alerta/:atv_id Elimina una alerta de vacuna
 * @apiVersion 0.2.0
 * @apiName DeleteVacunaAlerta
 *
 * @apiParam {Number} atv_id VacunaAlerta unique ID.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": registros eliminados correctamente
 *     }
 *
 * @apiError No se encuentra un consultorio con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "VacunaAlertaNotFound"
 *     }
 */
    
    public function destroy($atv_id)
    {
         $vacuna_alerta = \awebss\Models\Alerta_temprana_vacuna::find($atv_id);

        if (!$vacuna_alerta)
        {
            return response()->json(["mensaje"=>"no se encuentra una configuracion  con ese codigo"]);
        }

         $men_id=$vacuna_alerta->men_id;

         $mensaje_alerta=\awebss\Models\Mensaje_alerta::find($men_id);

         if (!$mensaje_alerta)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un mensaje asociado a esa configuracion con ese código.'])],404);
        }

       $vacuna_alerta->delete();

       $mensaje_alerta->delete();

        return response()->json([
            "mensaje" => "registros eliminados correctamente"
            ], 200
        );
    }
}
