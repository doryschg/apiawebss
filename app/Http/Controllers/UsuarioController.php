<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Hash;
use Carbon;
use Validator;
use awebss\Models\Funcionario_establecimiento;
use awebss\Models\Persona;
use awebss\User;
use awebss\Models\Rol_usuario;
use awebss\Models\Funcionario;

class UsuarioController extends Controller
{

public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]); 
    }

/**
 * @api {get} /usuarios Obtiene todos los usuarios
 * @apiVersion 0.2.0
 * @apiName GetUsuarios
 * @apiSuccess {array} usuario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError usuarios[], array vacio.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "UsuariosNotFound"
 *     }
 */ 

    public function index()
    {
        $usuario=User::all();
        return response()->json(['status'=>'ok',"msg"=>"exito",'usuario'=>$usuario], 200);
    }
/**
 * @api {get} /usuarios_establecimiento Obtiene los usuarios activos que pertenecen a un establecimiento
 * @apiVersion 0.2.0
 * @apiName GetUsuariosEstablecimientos
 * @apiParam {Number} es_id ID del establecimiento de salud
 * @apiSuccess {array} usuario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError usuario[], array vacio.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "UsuariosNotFound"
 *     }
 */ 
    public function usuarios_establecimiento($es_id)
    {
        
  $usuarios=Funcionario_establecimiento::where('es_id',$es_id)->join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->join('_usuario','_usuario.per_id','=','funcionario.per_id')->join('_rol_usuario','_rol_usuario.usu_id','=','_usuario.id')->join('_rol','_rol.rol_id','=','_rol_usuario.rol_id')->where('funcionario_establecimiento.fe_estado','ACTIVO')->whereNotIn('_rol.rol_id',[7])->whereNotIn('_rol.rol_id',[1])->where('_rol_usuario.ru_estado','ACTIVO')->select('funcionario_establecimiento.fe_id','persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','fe_cargo','_usuario.id','_rol.rol_id','rol_nombre','_rol_usuario.ru_id')->get();

        return response()->json(['status'=>'ok',"msg"=>"exito",'usuario'=>$usuarios], 200);
    }
/**
 * @api {post}/usuarios Crea información para usuario.
 * @apiVersion 0.2.0
 * @apiName PostUsuario
 * @apiSuccess {Array} usuario.
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
$per_id=$request->per_id;
$usuarios=User::where('per_id',$per_id)->first();
$count= count($usuarios);

if($count<=0)
{
$personas=Persona::find($per_id);
$per_ci=$personas->per_ci;
$contrasena= User::generar_contraseña($per_ci,$personas->per_fecha_nacimiento);
$usuarios= User::crear_cuenta($contrasena,$per_ci,$per_id);
$roles=Rol_usuario::crear_rol($usuarios->id,$request->rol_id);
}

else{
$roles=Rol_usuario::crear_rol($usuarios->id,$request->rol_id);}

$resultado=compact('usuario','roles');

    return response()->json(["msg" => "exito","usuario" => $resultado], 200);
    }
/**
 * @api {get} /usuarios Obtiene la informacion de un usuario
 * @apiVersion 0.2.0
 * @apiName GetUsuarios
 * @apiParam {Number} usu_id ID del usuario
 * @apiSuccess {array} usuario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un usuario con ese codigo
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "UsuariosNotFound"
 *     }
 */ 

 public function show($usu_id)
    {
        $usuario=User::find($usu_id);

         if (!$usuario)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un usuario con ese código.'])],404);
        }
        $per_id=$usuario->per_id;
        $persona=Persona::find($per_id);
        $roles=Rol_usuario::where('usu_id',$usu_id)->get();
        $resultado=compact('usuario','persona','roles');
        return response()->json([
                "msg" => "exito",
                "usuario" => $resultado
            ], 200
        );

    }
/**
 * @api {put} /usuarios Modifica informacion de usuario
 * @apiVersion 0.2.0
 * @apiName PutUsuario
 * @apiParam {Number} usu_id ID del usuario
 * @apiSuccess {array} usuario
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un usuario con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "UsuarioNotFound"
 *     }
 */ 

    public function update(Request $request, $usu_id)
    {
    $usuario=User::find($usu_id);
    if (!$usuario)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un usuario con ese código.'])],404);}
if($request->per_id==null)
{
        $validator = Validator::make($request->all(), [
            
            'new_password' => 'required|min:8',
        ]);

        if ($validator->fails()) 

        {
             return response()->json(["msg" => "exito", "usuario" => 'La contraseña debe ser mínimo 8 caracteres'], 200);  
        }

        $password_bd=$usuario->password;

        if (Hash::check($request->password, $password_bd))
        {

        $usuario->password=Hash::make($request->new_password);
        $usuario->save();
        return response()->json(["msg" => "exito", "usuario" => $usuario], 200);
        }
        else 
        {
          return response()->json(["msg" => "exito", "usuario" => 'Las contraseñas no coinciden'], 200);  
        }

        }

        $persona=Persona::find($request->per_id);

        $contrasena= User::generar_contraseña($persona->per_ci,$persona->per_fecha_nacimiento);
        $usuario->password=Hash::make($contrasena);
        $usuario->save();
        return response()->json(["msg" => "exito", "usuario" => $usuario], 200);
        
    }
/**
 * @api {get} /usuarios_estados Verifica que un usuario pertenesca a un establecimiento
 * @apiVersion 0.2.0
 * @apiName GetUsuariosEstados
 * @apiParam {Number} usu_id ID del usuario
 * @apiParam {Number} es_id ID del establecimiento de salud
 * @apiSuccess {bool} True/False dependiendo del resultado
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra un usuario con ese codigo
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "UsuariosNotFound"
 *     }
 */ 
      public function usuarios_estados($usu_id, $es_id)
    {
         $usuario=User::find($usu_id);
       
         if (!$usuario)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un usuario con ese código.'])],404);
        }
    $estado='false';
    $per_id=$usuario->per_id;
    $funcionario=Funcionario::where('per_id',$per_id)->first();
    $fun_id=$funcionario->fun_id;
    $funcionario_establecimiento=Funcionario_establecimiento::where('fun_id',$fun_id)->get();
    foreach ($funcionario_establecimiento as $funcionario ) {
    
        $es_id_f=$funcionario->es_id;
        if($es_id_f==$es_id)
        {
            $estado='true';
        }
    }

   return response()->json(["msg" => "exito", "usuario" => $estado], 200);
    }
/**
 * @api {get} /usuarios_cuetas permite la creacion de cuentas a pacientes, verificando se existe la cuenta usuario
 * @apiVersion 0.2.0
 * @apiName GetUsuariosCuentas
 * @apiParam {Number} per_id ID de la persona
 * @apiSuccess {number} 0/1 dependiendo del resultado 
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError  No se encuentra una persona con ese codigo
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "PersonaNotFound"
 *     }
 */ 

    public function usuarios_cuentas($per_id)
{
$persona =Persona::find($per_id);

 if (!$persona)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una persona con ese código.'])],404);
        }

$usuarios=User::where('per_id',$per_id)->first();
$count= count($usuarios);
if($count<=0)
{
    $personas=Persona::find($per_id);
    $per_ci=$personas->per_ci;
    $per_fecha_nacimiento=$personas->per_fecha_nacimiento;
    $contrasena= User::generar_contraseña($per_ci,$per_fecha_nacimiento);
    $usuarios= User::crear_cuenta($contrasena,$per_ci,$per_id);
    $roles=Rol_usuario::crear_rol($usuarios->id,7);
    $estado=1;
}
else
{
    $estado=0;
    $roles=Rol_usuario::where('usu_id',$usuarios->id)->where('rol_id',7)->get();
    $count_rol=count($roles);
    if($count_rol<=0) {$roles=Rol_usuario::crear_rol($usuarios->id,7);}
else {
    $estado=0;
}

}
return response()->json(["msg" => "exito","estado" => $estado], 200);
    }

/**
 * @api {delete} /usuarios/:usu_id Elimina usuarios
 * @apiVersion 0.2.0
 * @apiName DeleteUsuarios
 * @apiParam {Number} usu_id Usuario unique ID.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": registros eliminados correctamente
 *     }
 *
 * @apiError No se encuentra un usuario con con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "FamiliarNotFound"
 *     }
 */
    public function destroy($usu_id)
    {
        $usuario = User::find($usu_id);
 if (!$usuario)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un usuario con ese código.'])],404);
        }

        $rol_usuario=Rol_usuario::where('usu_id',$usu_id)->get();

        foreach ($rol_usuario as $rol) {
    
        $rol->delete();
        }

         $usuario->delete();

        return response()->json([
            "mensaje" => "registro eliminados correctamente"
            ], 200
        );
    }

}
