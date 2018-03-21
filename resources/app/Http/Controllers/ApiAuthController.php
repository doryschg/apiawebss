<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;
use awebss\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use awebss\User;

class ApiAuthController extends Controller
{   
    //Crear la funcion de autenticación
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['userAuth']]);
    }

    public function userAuth(Request $request)

    {   $credentials = $request->only('usu_nick', 'password');
    	$token = null; //donde se almacenara el token

    	try{   
            //con las credenciales de inicio de sesion se crea el token
    		if(!$token = JWTAuth::attempt($credentials)){
    			return response()->json(['error' => 'credenciales invalidos']);

                //return response()->json(['kkk' => $credentials]);
    		}
    	}catch(JWTException $ex){
    		return response()->json(['error' => 'algo no esta bien'], 500);
    	}
        //User relacionado con el token
        $user = JWTAuth::toUser($token);
        //return response()->json($user);
        $id=$user->id;
        $roles=\awebss\Models\Rol_usuario::where('usu_id',$id)->join('_rol','_rol.rol_id','=','_rol_usuario.rol_id')->select('_rol.rol_id','rol_nombre','_rol_usuario.ru_id','ru_estado','_rol_usuario.usu_id')->get();

    	return response()->json(compact('token', 'user','roles'));
    }

}
