<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Validator;
use awebss\Models\Configuracion_alerta;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ConfiguracionAlertaController extends Controller
{
    
     public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    } 
    
    public function index()
    {
    
    $configuracion_alerta=Configuracion_alerta::join('_mensaje_alerta','_mensaje_alerta.men_id','=','configuracion_alerta.men_id')
    ->whereNull('configuracion_alerta.deleted_at')->select('configuracion_alerta.ca_id','configuracion_alerta.enf_id','ca_edad_envio','ca_intervalo_inicio_envio','ca_intervalo_fin_envio','ca_frecuencia','ca_fecha_campania','ca_sexo','_mensaje_alerta.men_id','men_encabezado','men_cuerpo','men_despedida','men_tipo')->get();

    return response()->json(['status'=>'ok','configuracion_alerta'=>$configuracion_alerta],200); 

    }
/**
 * @api {post}/configuracion_alerta crea informacion para la configuracion de una alerta y tambien el mensaje
 * @apiVersion 0.2.0
 * @apiName PostConfiguracionAlerta
 * @apiSuccess {Array} configuracion_alerta
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
            
            'enf_id' => 'required', ]);

        if ($validator->fails()) 
        {
            return $validator->errors()->all();
        } 


        $men_tipo=$request->men_tipo;
        $enf_id=$request->enf_id;

    $enfermedad=\awebss\Models\Enfermedad::find($enf_id);

    $enf_nombre=$enfermedad->enf_nombre;
    

    if ($men_tipo=='campania')
    {
        $men_tipo="Campaña de  ".$enf_nombre;

        $mensaje_alerta= new \awebss\Models\Mensaje_alerta();
        $mensaje_alerta->men_encabezado=$request->men_encabezado;
        $mensaje_alerta->men_cuerpo=$request->men_cuerpo;
        $mensaje_alerta->men_despedida=$request->men_despedida;
        $mensaje_alerta->men_tipo=$men_tipo;
        $mensaje_alerta->userid_at=JWTAuth::toUser()->id;
        $mensaje_alerta->save();

        $configuracion_alerta= new \awebss\Models\Configuracion_alerta();
        $configuracion_alerta->enf_id=$enf_id;
        $configuracion_alerta->men_id=$mensaje_alerta->men_id;
        $configuracion_alerta->ca_edad_envio=$request->ca_edad_envio;
        $configuracion_alerta->ca_intervalo_inicio_envio=$request->ca_intervalo_inicio_envio;
        $configuracion_alerta->ca_intervalo_fin_envio=$request->ca_intervalo_fin_envio;
        $configuracion_alerta->ca_frecuencia=0;
        $configuracion_alerta->ca_fecha_campania=$request->ca_fecha_campania;
        $configuracion_alerta->ca_sexo=$request->ca_sexo;
        $configuracion_alerta->userid_at=JWTAuth::toUser()->id;
        $configuracion_alerta->save();

        $resultado=compact('mensaje_alerta','configuracion_alerta');

         return response()->json(['status'=>'ok','mensaje'=>'exito','configuracion_alerta'=>$resultado],200); 

    }

    if($men_tipo=='prevencion')
    {
        $men_tipo="Prevencion de  ".$enf_nombre;

        $mensaje_alerta= new \awebss\Models\Mensaje_alerta();
        $mensaje_alerta->men_encabezado=$request->men_encabezado;
        $mensaje_alerta->men_cuerpo=$request->men_cuerpo;
        $mensaje_alerta->men_despedida=$request->men_despedida;
        $mensaje_alerta->men_tipo=$men_tipo;
        $mensaje_alerta->userid_at=JWTAuth::toUser()->id;
        $mensaje_alerta->save();

        $configuracion_alerta= new \awebss\Models\Configuracion_alerta();
        $configuracion_alerta->enf_id=$enf_id;
        $configuracion_alerta->men_id=$mensaje_alerta->men_id;
        $configuracion_alerta->ca_edad_envio=$request->ca_edad_envio;
        $configuracion_alerta->ca_intervalo_inicio_envio=$request->ca_intervalo_inicio_envio;
        $configuracion_alerta->ca_intervalo_fin_envio=$request->ca_intervalo_fin_envio;
        $configuracion_alerta->ca_frecuencia=$request->ca_frecuencia;
        $configuracion_alerta->ca_fecha_campania=$request->ca_fecha_campania;
        $configuracion_alerta->ca_sexo=$request->ca_sexo;
        $configuracion_alerta->userid_at=JWTAuth::toUser()->id;
        $configuracion_alerta->save();

        $resultado=compact('mensaje_alerta','configuracion_alerta');

         return response()->json(['status'=>'ok','mensaje'=>'exito','configuracion_alerta'=>$resultado],200); }
    }
/**
 * @api {get} /configuracion_alerta Obtiene informacion de una configuracion alerta
 * @apiVersion 0.2.0
 * @apiName GetConfiguracion_alerta
 * @apiParam {Number} atv_id ID de la configuracion_alerta
 * @apiSuccess {array} configuracion_alerta
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra una configuracion alerta con ese codigo
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ConfiguracionAlertaNotFound"
 *     }
 */    
    public function show($ca_id)
    {
        $configuracion_alerta=\awebss\Models\Configuracion_alerta::find($ca_id);

         if (!$configuracion_alerta)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una configuracion alerta con ese código.'])],404);
        }

    $men_id=$configuracion_alerta->men_id;

    $mensaje_alerta=\awebss\Models\Mensaje_alerta::find($men_id)->toArray();
    $configuracion_alerta=$configuracion_alerta->toArray();

    $resultado=array_collapse([$configuracion_alerta,$mensaje_alerta]);

     return response()->json(['status'=>'ok','mensaje'=>'exito','configuracion_alerta'=>$resultado],200); 
    }
/**
 * @api {put} /configuracion_alerta/:atv_id modifica los campos de una configuracion alerta
 * @apiVersion 0.2.0
 * @apiName PutConfiguracionAlerta
 
 * @apiParam {Number} atv_id configuracion_alertaunique ID.
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
 *       "error": "ConfiguracionAlertaNotFound"
 *     }
 */ 
  
    public function update(Request $request, $ca_id)
    {

        $configuracion_alerta=\awebss\Models\Configuracion_alerta::find($ca_id);

        $men_id=$configuracion_alerta->men_id;

         if (!$configuracion_alerta)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una configuracion alerta con ese código.'])],404);
        }

         $mensaje_alerta=\awebss\Models\Mensaje_alerta::find($men_id);

         if (!$mensaje_alerta)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un mensaje asociado a esa configuracion con ese código.'])],404);
        }
        
        $mensaje_alerta->men_encabezado=$request->men_encabezado;
        $mensaje_alerta->men_cuerpo=$request->men_cuerpo;
        $mensaje_alerta->men_despedida=$request->men_despedida;
        $mensaje_alerta->save();

        $configuracion_alerta->ca_edad_envio=$request->ca_edad_envio;
        $configuracion_alerta->ca_intervalo_inicio_envio=$request->ca_intervalo_inicio_envio;
        $configuracion_alerta->ca_intervalo_fin_envio=$request->ca_intervalo_fin_envio;
      
        $configuracion_alerta->ca_fecha_campania=$request->ca_fecha_campania;
        $configuracion_alerta->ca_sexo=$request->ca_sexo;
       
        $configuracion_alerta->save();

         
        $resultado=compact('mensaje_alerta','configuracion_alerta');

         return response()->json(['status'=>'ok','mensaje'=>'exito','configuracion_alerta'=>$resultado],200);   }

/**
 * @api {delete} /configuracion_alerta/:atv_id Elimina una configuracion de alerta
 * @apiVersion 0.2.0
 * @apiName DeleteConfiguracionAlerta
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
 * @apiError No se encuentra una configuracion alerta con con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ConfiguracionAlertaNotFound"
 *     }
 */
    public function destroy($ca_id)
    {

        $configuracion_alerta = \awebss\Models\Configuracion_alerta::find($ca_id);

        if (!$configuracion_alerta)
        {
            return response()->json(["mensaje"=>"no se encuentra una configuracion  con ese codigo"]);
        }

         $men_id=$configuracion_alerta->men_id;

         $mensaje_alerta=\awebss\Models\Mensaje_alerta::find($men_id);

         if (!$mensaje_alerta)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un mensaje asociado a esa configuracion con ese código.'])],404);
        }

       $configuracion_alerta->delete();

       $mensaje_alerta->delete();

        return response()->json([
            "mensaje" => "registros eliminados correctamente"
            ], 200
        );
        
    }
}
