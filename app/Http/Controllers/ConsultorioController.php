<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ConsultorioController extends Controller
{

     public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    } 

/**
 * @api {get} /consultorios Obtiene los consultorios de un establecimiento de salud
 * @apiVersion 0.2.0
 * @apiName GetConsultorio
 * @apiSuccess {array} servicio_establecimiento, servicio_establecimiento[], array vacio
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
 *       "error": "ConsultorioNotFound"
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

$servicio_establecimiento =\awebss\Models\Servicio_establecimiento::where('es_id',$es_id)->join('servicio_consultorio','servicio_consultorio.se_id','=','servicio_establecimiento.se_id')->join('consultorio','consultorio.con_id','=','servicio_consultorio.con_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->whereNull('consultorio.deleted_at')->select('consultorio.con_id','con_nombre','servicio.ser_id','ser_nombre')->orderBy('consultorio.con_nombre')->get();
$count=count($servicio_establecimiento);

return response()->json(['status'=>'ok',"msg" => "exito",'consultorio'=>$servicio_establecimiento],200); 

    }

/**
 * @api {get} /consultorios Obtiene los servicios de un consultorio
 * @apiVersion 0.2.0
 * @apiName GetConsultorio
 * @apiParam {Number} con_id ID del consultorio
 * @apiSuccess {array} consultorio
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError consultorio[]
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ConsultorioNotFound"
 *     }
 */ 
    
public function show($con_id)
	{

$consultorio =\awebss\Models\Consultorio::find($con_id);
 if (!$consultorio)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un consultorio con ese código.'])],404);
        }

$servicio_consultorio=\awebss\Models\Servicio_consultorio::where('con_id',$con_id)->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->select('servicio_consultorio.sc_id','con_id','servicio_establecimiento.se_id','servicio.ser_id','ser_nombre','ser_tipo')->get();

$resultado=compact('consultorio','servicio_consultorio');

return response()->json(['status'=>'ok',"msg" => "exito",'consultorio'=>$resultado],200); 

    }

/**
 * @api {get} /consultorios_establecimientos Obtiene los consultorios de un establecimeinto
 * @apiVersion 0.2.0
 * @apiName GetConsultorio
 * @apiParam {Number} es_id ID del establecimiento
 * @apiSuccess {array} consultorio
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError consultorio[]
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ConsultorioNotFound"
 *     }
 */ 
    
public function listar_consultorio_es($es_id)
    {
$servicio_establecimiento =\awebss\Models\Servicio_establecimiento::where('es_id',$es_id)->join('servicio_consultorio','servicio_consultorio.se_id','=','servicio_establecimiento.se_id')->join('consultorio','consultorio.con_id','=','servicio_consultorio.con_id')->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->whereNull('consultorio.deleted_at')->select('consultorio.con_id','con_nombre')->distinct('consultorio.con_id')->get();

/* $count=count($servicio_establecimiento);

    if($count<=0)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No existen consultorios en ese establecimiento.'])],404); 
        }*/
return response()->json(['status'=>'ok',"msg" => "exito",'consultorio'=>$servicio_establecimiento],200); 

    }

/**
 * @api {post}/consultorios Crea información para un consultorio
 * @apiVersion 0.2.0
 * @apiName PostConsultorio
 * @apiSuccess {Array} consultorio.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError BadResponse El consultorio no ha podido crearse
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
            
            'con_nombre' => 'required',
            'con_cod' => 'required',
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }
        $consultorio = new \awebss\Models\Consultorio();
        $consultorio->userid_at=JWTAuth::toUser()->id;
        $consultorio->con_nombre = $request->con_nombre;
        $consultorio->con_tipo = $request->con_tipo;
        $consultorio->con_descripcion = $request->con_descripcion;
        $consultorio->con_cod=$request->con_cod; 
        $consultorio->save();

        return response()->json([
                'status'=>'ok',"msg" => "exito",
          "consultorio" => $consultorio
            ], 200
        ); 
    }

/**
 * @api {put} /consultorios/:con_id modifica los campos de un consultorio.
 * @apiVersion 0.2.0
 * @apiName PutConsultorio
 * @apiParam {Number} con_id Consultorio unique ID.
 * @apiSuccess {Array} consultorio
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra un consutorio con ese código.'
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ConsultorioNotFound"
 *     }
 */ 

     public function update(Request $request, $con_id)
    {
         $validator = Validator::make($request->all(), [
            
            'con_nombre' => 'required',
            'con_cod' => 'required',
        ]);

        if ($validator->fails()) 

        {
            return $validator->errors()->all();
        }
        $consultorio = \awebss\Models\Consultorio::find($con_id);

         if (!$consultorio)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No existen consultorios con ese codigo.'])],404);
        }
        $input = $request->all();
       
        $consultorio->update($input);

        return response()->json(['status'=>'ok',"msg" => "exito",
          "consultorio" => $consultorio
            ], 200
            ); 
    }

/**
 * @api {delete} /consultorios/:con_id Elimina un consultorio en cascada
 * @apiVersion 0.2.0
 * @apiName DeleteConsultorio
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
 * @apiError No se encuentra un consultorio con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ConsultorioNotFound"
 *     }
 */

      public function destroy($con_id)
    {
        $consultorio =\awebss\Models\Consultorio::find($con_id);

 if (!$consultorio)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un consultorio con ese código.'])],404);
        }

$servicio_consultorio=\awebss\Models\Servicio_consultorio::where('con_id',$con_id);

foreach ($servicio_consultorio as $servicio) {

    $servicio->delete();
}

        $consultorio->delete();

 return response()->json([
            "mensaje" => "registros eliminado correctamente"
            ], 200
        );
        
    }

}
