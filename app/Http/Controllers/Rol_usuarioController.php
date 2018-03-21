<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;

use awebss\Models\Rol_usuario;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class Rol_usuarioController extends Controller
{
  
    public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['update']]);
    }

/**
 * @api {put} /roles_usuarios/:ru_id modifica los campos de rol usuario. 
 * @apiVersion 0.2.0
 * @apiName PutRolUsuario
 
 * @apiParam {Number} ru_id rol_usuario unique ID.
 * @apiSuccess {Array} cita
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra un rol de usuario con ese código.'
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "NotFound"
 *     }
 */ 
    public function update(Request $request, $ru_id)
    {
    
$roles=Rol_usuario::find($ru_id);

   if (!$roles)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un rol con ese código.'])],404);
        }
        
       $roles->rol_id=$request->rol_id;
       $roles->ru_estado=$request->ru_estado;
       $roles->save(); 
       
return response()->json(['status'=>'ok',"msg" => "exito","roles" => $roles], 200);

    }

}
