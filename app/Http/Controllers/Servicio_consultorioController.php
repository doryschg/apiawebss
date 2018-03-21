<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;
use Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use awebss\Models\Servicio_consultorio;

class Servicio_consultorioController extends Controller
{

     public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','destroy']]);
    } 
/**
 * @api {post}/servicios_consultorios Crea información para un servicio de un consultorio
 * @apiVersion 0.2.0
 * @apiName PostServicio_consultorio
 * @apiSuccess {Array} servicio_consultorio
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
            
            'con_id' => 'required',
            'se_id' => 'required',
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }

        $servicio_consultorio= new Servicio_consultorio();

        $servicio_consultorio->con_id=$request->con_id;
        $servicio_consultorio->se_id=$request->se_id;
        $servicio_consultorio->userid_at=JWTAuth::toUser()->id;
        $servicio_consultorio->save();

        return response()->json([
                "msg" => "exito",'status'=>'ok',"msg" => "exito",
          "consultorio" => $servicio_consultorio
            ], 200
        ); 
    }
/**
 * @api {get} /servicios_consultorios Obtiene los consultorios de un servicio consultorio
 * @apiVersion 0.2.0
 * @apiName GetServicioConsultorio
 * @apiParam {Number} sc_id ID del servicio_consultorio
 * @apiSuccess {array} servicio_consultorio
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un servicio_consultorio con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "PersonaNotFound"
 *     }
 */ 
   public function show($sc_id)
    {
        $servicio_consultorio=Servicio_consultorio::find($sc_id);

     if (!$servicio_consultorio)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un servicio consultorio con ese código.'])],404);
        }
        
     $servicio_consultorio = \awebss\Models\Servicio_consultorio::join('consultorio','consultorio.con_id','=','servicio_consultorio.con_id')->select('consultorio.con_id','con_nombre','con_tipo','con_descripcion')->whereNull('consultorio.deleted_at')->where('servicio_consultorio.sc_id',$sc_id)->get();
    
        return response()->json(['status'=>'ok',"msg" => "exito",'consultorio'=>$servicio_consultorio],200);
    }  
/**
 * @api {delete} /servicios_consultorios/:sc_id Elimina un servicio consultorio
 * @apiVersion 0.2.0
 * @apiName DeleteServicioConsultorio
 *
 * @apiParam {Number} sc_id servicio_consultorio unique ID.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": registros eliminados correctamente
 *     }
 *
 * @apiError No se encuentra un servicio consultorio con con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "ServicioConsultorioNotFound"
 *     }
 */
    public function destroy($sc_id)
    {
        //

     $servicio_consultorio =Servicio_consultorio::find($sc_id);

 if (!$servicio_consultorio)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un servicio consultorio con ese código.'])],404);
        }

        $servicio_consultorio->delete();

 return response()->json([
            "mensaje" => "registros eliminado correctamente"
            ], 200
        );


    }
}
