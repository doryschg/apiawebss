<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;
use awebss\Models\Rol;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RolController extends Controller
{

    
      public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store']]);
    }
    
    
    public function index()
    {
        $rol=\awebss\Models\Rol::all();

        return response()->json(['status'=>'ok',"msg"=>"exito",'rol'=>$rol], 200);
    }

    public function store(Request $request)
    {
        $rol= new \awebss\Models\Rol();
       
        $rol->rol_nombre = $request->rol_nombre;
        $rol->rol_descripcion= $request->rol_descripcion;
        
        $rol->save();

         return response()->json([
                "msg" => "exito",
                "rol" => $rol
            ], 200
        );
    }
    public function show($rol_id)
    {
        $rol=\awebss\Models\Rol::find($rol_id);

         if (!$rol)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un rol con ese código.'])],404);
        }

        return response()->json(['status'=>'ok',"msg"=>"exito",'rol'=>$rol], 200);
    }

    public function roles_permisos($rol_id)
    {
$roles=Rol::join('_rol_servicio','_rol_servicio.rol_id','=','_rol.rol_id')
->join('_servicio','_servicio.ser_id','=','_rol_servicio.ser_id')
->where('_rol_servicio.rol_id','=',$rol_id)
->select('_rol.rol_id','rol_nombre','rol_descripcion','_servicio.ser_id','ser_nombre','ser_uri')->get();

 if (!$roles)
        {
    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentran permisos asociados a este rol.'])],404);
        }


return response()->json(['status'=>'ok',"msg"=>"exito",'rol'=>$roles], 200);

    }

}
