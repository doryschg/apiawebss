<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Validator;
use Illuminate\Support\Str;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use awebss\Models\Familiar;
use awebss\Models\Persona;
use awebss\Models\Imagen;
use awebss\Models\Direccion;

class FamiliarController extends Controller
{    
     public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','crear_persona_familiar','destroy']]);
    }
/**
 * @api {post}/familiar Crea información para familiares.
 * @apiVersion 0.2.0
 * @apiName PostFamiliar
 * @apiSuccess {Array} familiar.
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

        $fam_parentesco=$request->fam_parentesco;


        if($fam_parentesco=='MADRE'|| $fam_parentesco=='PADRE')
        {

        $familiar=new Familiar();
        $familiar->per_id=$request->per_id_familiar;
        $familiar->per_id_familiar=$request->per_id;
        $familiar->fam_parentesco='HIJO(A)';
        $familiar->userid_at=JWTAuth::toUser()->id;
        $familiar->save();
        
        if($fam_parentesco=='MADRE')
        {
        $familiar2=new Familiar();
        $familiar2->per_id=$request->per_id;
        $familiar2->per_id_familiar=$request->per_id_familiar;
        $familiar2->fam_parentesco='MADRE';
        $familiar2->userid_at=JWTAuth::toUser()->id;
        $familiar2->save();
        }

        else {

        $familiar2=new Familiar();
        $familiar2->per_id=$request->per_id;
        $familiar2->per_id_familiar=$request->per_id_familiar;
        $familiar2->fam_parentesco='PADRE';
        $familiar2->userid_at=JWTAuth::toUser()->id;
        $familiar2->save();

        }

        }

        if($fam_parentesco=='HERMANO(A)')
        {

        $familiar=new Familiar();
        $familiar->per_id=$request->per_id_familiar;
        $familiar->per_id_familiar=$request->per_id;
        $familiar->fam_parentesco='HERMANO(A)';
        $familiar->userid_at=JWTAuth::toUser()->id;
        $familiar->save();
        
        $familiar2=new Familiar();
        $familiar2->per_id=$request->per_id;
        $familiar2->per_id_familiar=$request->per_id_familiar;
        $familiar2->fam_parentesco='HERMANO(A)';
        $familiar2->userid_at=JWTAuth::toUser()->id;
        $familiar2->save();

        }
        if($fam_parentesco=='TUTOR(A)')
        {

        $familiar=new Familiar();
        $familiar->per_id=$request->per_id_familiar;
        $familiar->per_id_familiar=$request->per_id;
        $familiar->fam_parentesco='TUTELADO(A)';
        $familiar->userid_at=JWTAuth::toUser()->id;
        $familiar->save();
        
        $familiar2=new Familiar();
        $familiar2->per_id=$request->per_id;
        $familiar2->per_id_familiar=$request->per_id_familiar;
        $familiar2->fam_parentesco='TUTOR(A)';
        $familiar2->userid_at=JWTAuth::toUser()->id;
        $familiar2->save();

        }

        $resultado=compact('familiar','familiar2');

         return response()->json(["msg" => "exito","familiar" => $resultado], 200);
    }
/**
 * @api {get} /familiar Obtiene informacion de familiares de una persona
 * @apiVersion 0.2.0
 * @apiName GetFamiliar
 * @apiParam {Number} per_id ID de la persona
 * @apiSuccess {array} familiar
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra una persona con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "PersonaNotFound"
 *     }
 */ 
     public function show($per_id)
    {
        $persona=Persona::find($per_id);


     if (!$persona)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una persona con ese código.'])],404);
        }

    $familiares=\awebss\Models\Familiar::select('familiar.fam_id','persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','familiar.fam_id','fam_parentesco')->join('persona','persona.per_id','=','familiar.per_id_familiar')->where('familiar.per_id',$per_id)->get();

    $resultado=compact('persona','familiares');

 return response()->json(['status'=>'ok','mensaje'=>'exito','familiar'=>$resultado],200); 
    }
/**
 * @api {post}/personas_familiar Crea información de una persona y sus familiare .
 * @apiVersion 0.2.0
 * @apiName PostPersona_familiar
 * @apiSuccess {Array} familiar.
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

    public function crear_persona_familiar(Request $request)
{
    $validator = Validator::make($request->all(), [
            
            'per_id' => 'required',
            'mun_id' => 'required',
            'per_ci' => 'required', ]);

        if ($validator->fails()) 
        {
            return $validator->errors()->all();
        }  

        $personas = new Persona();
        $personas->per_ci = $request->per_ci;
        $personas->per_ci_expedido = $request->per_ci_expedido;
        $personas->per_nombres= Str::upper($request->per_nombres);
        $personas->per_apellido_primero= Str::upper($request->per_apellido_primero);
        $personas->per_apellido_segundo= Str::upper($request->per_apellido_segundo);
        $personas->per_fecha_nacimiento= $request->per_fecha_nacimiento;
        $personas->per_genero= $request->per_genero;
        $personas->per_email= $request->per_email;
        $personas->per_tipo_permanencia= $request->per_tipo_permanencia;
        $personas->per_numero_celular= $request->per_numero_celular;
        $personas->per_clave_publica= $request->per_clave_publica; 
        $personas->userid_at=JWTAuth::toUser()->id;
        $personas->save();

        //creando la imagen de la persona

        $imagen = new Imagen();
        $imagen->per_id=$personas->per_id;
        $imagen->ima_nombre=$request->ima_nombre;
        $imagen->ima_enlace=$request->ima_enlace;
        $imagen->ima_tipo=$request->ima_tipo;
        $imagen->userid_at=JWTAuth::toUser()->id;
         $imagen->save();

        // creando la direccion del a persona
        $direcciones = new Direccion();
        $direcciones->per_id=$personas->per_id;
        $direcciones->mun_id=$request->mun_id;
        $direcciones->dir_zona_comunidad= Str::upper($request->dir_zona_comunidad);
        $direcciones->dir_avenida_calle= Str::upper($request->dir_avenida_calle);
        $direcciones->dir_numero=$request->dir_numero;
        $direcciones->dir_tipo=$request->dir_tipo;
        $direcciones->userid_at=JWTAuth::toUser()->id;
        $direcciones->save();


        $fam_parentesco=$request->fam_parentesco;


        if($fam_parentesco=='MADRE'|| $fam_parentesco=='PADRE')
        {

        $familiar=new Familiar();
        $familiar->per_id=$personas->per_id;
        $familiar->per_id_familiar=$request->per_id;
        $familiar->fam_parentesco='HIJO(A)';
        $familiar->userid_at=JWTAuth::toUser()->id;
        $familiar->save();
        
        if($fam_parentesco=='MADRE')
        {
        $familiar2=new Familiar();
        $familiar2->per_id=$request->per_id;
        $familiar2->per_id_familiar=$personas->per_id;
        $familiar2->fam_parentesco='MADRE';
        $familiar2->userid_at=JWTAuth::toUser()->id;
        $familiar2->save();
        }

        else {

        $familiar2=new Familiar();
        $familiar2->per_id=$request->per_id;
        $familiar2->per_id_familiar=$personas->per_id;
        $familiar2->fam_parentesco='PADRE';
        $familiar2->userid_at=JWTAuth::toUser()->id;
        $familiar2->save();

        }

        }

        if($fam_parentesco=='HERMANO(A)')
        {

        $familiar=new Familiar();
        $familiar->per_id=$personas->per_id;
        $familiar->per_id_familiar=$request->per_id;
        $familiar->fam_parentesco='HERMANO(A)';
        $familiar->userid_at=JWTAuth::toUser()->id;
        $familiar->save();
        
        $familiar2=new Familiar();
        $familiar2->per_id=$request->per_id;
        $familiar2->per_id_familiar=$personas->per_id;
        $familiar2->fam_parentesco='HERMANO(A)';
        $familiar2->userid_at=JWTAuth::toUser()->id;
        $familiar2->save();

        }

         if($fam_parentesco=='TUTOR(A)')
        {

        $familiar=new Familiar();
        $familiar->per_id=$personas->per_id;
        $familiar->per_id_familiar=$request->per_id;
        $familiar->fam_parentesco='TUTELADO(A)';
        $familiar->userid_at=JWTAuth::toUser()->id;
        $familiar->save();
        
        $familiar2=new Familiar();
        $familiar2->per_id=$personas->per_id;
        $familiar2->per_id_familiar=$request->per_id;
        $familiar2->fam_parentesco='TUTOR(A)';
        $familiar2->userid_at=JWTAuth::toUser()->id;
        $familiar2->save();

        }


        $resultado=compact('personas','imagen','direcciones','familiar','familiar2');

         return response()->json([
                "msg" => "exito",
                "persona" => $resultado
            ], 200
        );
}

/**
 * @api {put} /familiar Obtiene informacion de familiares de una persona
 * @apiVersion 0.2.0
 * @apiName PutFamiliar
 * @apiParam {Number} per_id ID de la persona
 * @apiSuccess {array} familiar
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra una persona con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "PersonaNotFound"
 *     }
 */ 
    public function update(Request $request, $per_id)
    {
         $persona=Persona::find($per_id);

     if (!$persona)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una persona con ese código.'])],404);
        }

    $familiares=Familiar::select('familiar.fam_id','persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','familiar.fam_id','fam_parentesco','paciente.pac_id')->join('persona','persona.per_id','=','familiar.per_id_familiar')->join('paciente','paciente.per_id','=','familiar.per_id_familiar')->where('familiar.per_id',$per_id)->get();

    $resultado=compact('persona','familiares');

 return response()->json(['status'=>'ok','mensaje'=>'exito','familiar'=>$resultado],200); 
         }
/**
 * @api {delete} /familiar/:fam_id Elimina una al familiar de una persona
 * @apiVersion 0.2.0
 * @apiName DeleteFamiliar
 *
 * @apiParam {Number} fam_id Familiar unique ID.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": registros eliminados correctamente
 *     }
 *
 * @apiError No se encuentra un familiar con con ese código.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "FamiliarNotFound"
 *     }
 */
public function destroy($fam_id)
    {
        
$familiar =Familiar::find($fam_id);

 if (!$familiar)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un familiar con ese código.'])],404);
        }
$per_id=$familiar->per_id;
$per_id_familiar=$familiar->per_id_familiar;

$familiar2=Familiar::where('per_id',$per_id_familiar)->where('per_id_familiar',$per_id)->get()->first();

$familiar2->delete();
$familiar->delete();

 return response()->json([
            "mensaje" => "registros eliminados correctamente"
            ], 200
        );
        
    } 

   
}
