<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Illuminate\Support\Facades\DB;
use Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use awebss\Models\Persona;
use awebss\Models\Paciente_establecimiento;
use awebss\Models\Establecimiento_salud;
use awebss\Models\Paciente;
use awebss\Models\Boleta_referencia;
use awebss\Models\Boleta_contrareferencia;
use awebss\Models\Funcionario_establecimiento;
use awebss\Models\Direccion;

class Boleta_contrareferenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['update','store','destroy','editar']]));
    }  
/**
 * @api {get} /contrareferencia Obtiene todas las boletas de contrareferencia
 * @apiVersion 0.2.0
 * @apiName GetContrareferencia
 * @apiSuccess {array} contrareferencia[]
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 */   
    public function index()
    {
        $contrareferencia=Boleta_contrareferencia::all();

          return response()->json(['status'=>'ok','contrareferencia'=>$contrareferencia],200); 
    }
/**
 * @api {get} /contrareferencias_establecimientos_origen Obtiene todas las boletas de contrareferencia de un establecimiento origen
 * @apiVersion 0.2.0
 * @apiName GetReContrareferenciaOrigen
 * @apiParam {Number} es_id ID del establecimiento de salud
 * @apiSuccess {array} contrareferencia[]
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
  * @apiError No se encuentra un establecimiento con ese codigo
 */ 
     public function lista_contrareferencia($es_id_origen)
    { 
        $establecimiento=Establecimiento_salud::find($es_id_origen);
         
          if (!$establecimiento)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un establecimiento con ese código.'])],404);
        }
    $contrareferencia=Boleta_contrareferencia::select('establecimiento_salud.es_id','es_nombre','persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','boleta_referencia.br_id','boleta_contrareferencia.bc_id','bc_cod','bc_estado_contrareferencia','boleta_contrareferencia.created_at')->join('boleta_referencia','boleta_referencia.br_id','=','boleta_contrareferencia.br_id')
    ->join('establecimiento_salud','establecimiento_salud.es_id','=','boleta_referencia.es_id_destino')
    ->join('paciente','paciente.pac_id','=','boleta_referencia.pac_id')
    ->join('persona','persona.per_id','=','paciente.per_id')
    ->where('boleta_referencia.es_id_origen','=',$es_id_origen)->get();
    
          return response()->json(["mensaje"=>"exito","contrareferencia"=>$contrareferencia],200); 
    }
/**
 * @api {get} /contrareferencias_establecimientos_destino Obtiene todas las boletas de contrareferencia de un establecimiento destino
 * @apiVersion 0.2.0
 * @apiName GetReContrareferenciaDestino
 * @apiParam {Number} es_id ID del establecimiento de salud
 * @apiSuccess {array} referencia[]
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
  * @apiError No se encuentra un establecimiento con ese codigo
 */ 

      public function lista_contrareferencia_destino($es_id_destino)
    {
        $establecimiento=Establecimiento_salud::find($es_id_destino);
          if (!$establecimiento)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un establecimiento con ese código.'])],404);
        }
        $contrareferencia=Boleta_contrareferencia::select('establecimiento_salud.es_id','es_nombre','persona.per_id','per_nombres','per_apellido_primero','per_apellido_segundo','boleta_referencia.br_id','boleta_contrareferencia.bc_id','bc_cod','bc_estado_contrareferencia','boleta_contrareferencia.fe_id_origen','boleta_contrareferencia.created_at')->join('boleta_referencia','boleta_referencia.br_id','=','boleta_contrareferencia.br_id')
    ->join('establecimiento_salud','establecimiento_salud.es_id','=','boleta_referencia.es_id_origen')
    ->join('paciente','paciente.pac_id','=','boleta_referencia.pac_id')
    ->join('persona','persona.per_id','=','paciente.per_id')
    ->where('boleta_referencia.es_id_destino',$es_id_destino)->get();

          return response()->json(['status'=>'ok','contrareferencia'=>$contrareferencia],200);
    } 
/**
 * @api {post}/contrareferencia Crea información para una contrareferencia
 * @apiVersion 0.2.0
 * @apiName PostContrareferencia
 * @apiSuccess {Array} contrareferencia.
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
            
            'br_id' => 'required',
            'bc_cod' => 'required',
            'fe_id_origen'=>'required',
        ]);

        if ($validator->fails()) 

        {
            return $validator->errors()->all();
        }  
        $contrareferencia= new \awebss\Models\Boleta_contrareferencia();
        $contrareferencia->br_id=$request->br_id;
        $contrareferencia->fe_id_destino=$request->fe_id_destino;
        $contrareferencia->fe_id_contacto=$request->fe_id_contacto;
        $contrareferencia->fe_id_origen=$request->fe_id_origen;
        $contrareferencia->bc_cod=$request->bc_cod;
        $contrareferencia->bc_servicio_referente=$request->bc_servicio_referente;
        $contrareferencia->bc_dias_internacion=$request->bc_dias_internacion;
        $contrareferencia->bc_peso=$request->bc_peso;
        $contrareferencia->bc_temp=$request->bc_temp;
        $contrareferencia->bc_pa_sistolica=$request->bc_pa_sistolica;
        $contrareferencia->bc_fc=$request->bc_fc;
        $contrareferencia->bc_fr=$request->bc_fr;
        $contrareferencia->bc_diagnostico_egreso=$request->bc_diagnostico_egreso;
        $contrareferencia->bc_complicaciones=$request->bc_complicaciones;
        $contrareferencia->bc_examenes_dx=$request->bc_examenes_dx;
        $contrareferencia->bc_exa_interconsultas=$request->bc_exa_interconsultas;
        $contrareferencia->bc_tratamientos=$request->bc_tratamientos;
        $contrareferencia->bc_seguimento_trat=$request->bc_seguimento_trat;
        $contrareferencia->bc_recomendaciones=$request->bc_recomendaciones;
        $contrareferencia->bc_referencia_fue=$request->bc_referencia_fue;
        $contrareferencia->bc_acomp=$request->bc_acomp;
        $contrareferencia->bc_fecha_llegada=$request->bc_fecha_llegada;
        $contrareferencia->bc_hora_llegada=$request->bc_hora_llegada;
        $contrareferencia->bc_estado_contrareferencia=$request->bc_estado_contrareferencia;
        $contrareferencia->bc_pa_diastolica=$request->bc_pa_diastolica;
        $contrareferencia->bc_talla=$request->bc_talla;
        $contrareferencia->userid_at=JWTAuth::toUser()->id;
        $contrareferencia->save();
        $br_id=$contrareferencia->br_id;
        $referencia=Boleta_referencia::find($br_id);
        $es_id_destino=$referencia->es_id_destino;
        $pac_id=$referencia->pac_id;
        $paciente_establecimiento=Paciente_establecimiento::where('pac_id',$pac_id)->get();
        foreach($paciente_establecimiento as $paciente)
        {  
        $es_id=$paciente->es_id;

        if($es_id==$es_id_destino)
        { 
        $pe_id=$paciente->pe_id; 
        $paciente_es = Paciente_establecimiento::find($pe_id);
        $paciente_es->pe_estado='INACTIVO';
        $paciente_es->save();} }
        $contrareferencia=compact('contrareferencia','paciente_es');
        return response()->json(['status'=>'ok','contrareferencia'=>$contrareferencia],200); 

    }
/**
 * @api {get} /contrareferencia Obtiene los datos de una contrareferencia
 * @apiVersion 0.2.0
 * @apiName GetContrareferencia
 * @apiParam {Number} bc_id ID de la contrareferencia
 * @apiSuccess {array} contrareferencia
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje"exito: 
 *     }
 *
 * @apiError No se encuentra una contrareferenciareferencia con ese codigo
 */

public function show($bc_id)
    {
        $contrareferencia=Boleta_contrareferencia::find($bc_id);

        if (!$contrareferencia)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una contrareferencia con ese código.'])],404);
        }
        $br_id=$contrareferencia->br_id;
        $referencia=Boleta_referencia::find($br_id);
        $es_id_origen=$referencia->es_id_origen;
        $fe_id_contacto=$contrareferencia->fe_id_contacto;
        $establecimiento_origen=Establecimiento_salud::join('red','red.red_id','=','establecimiento_salud.red_id')
        ->join('municipio','municipio.mun_id','=','establecimiento_salud.mun_id')
        ->where('establecimiento_salud.es_id',$es_id_origen)
        ->select('establecimiento_salud.es_id','es_nombre','red.red_id','red_nombre','municipio.mun_id','mun_nombre')->get();
        $es_id_destino=$referencia->es_id_destino;
        $establecimiento_destino=Establecimiento_salud::join('red','red.red_id','=','establecimiento_salud.red_id')
        ->join('municipio','municipio.mun_id','=','establecimiento_salud.mun_id')
        ->where('establecimiento_salud.es_id',$es_id_destino)->select('establecimiento_salud.es_id','es_nombre','red.red_id','red_nombre','municipio.mun_id','mun_nombre')->get();
       $pac_id=$referencia->pac_id;
       $paciente=\awebss\Models\Paciente::find($pac_id);
       $per_id=$paciente->per_id;
       $direccion=\awebss\Models\Direccion::where('per_id',$per_id)->get();
       $persona_paciente=Persona::find($per_id);
       $fe_id_origen=$contrareferencia->fe_id_origen;
       $funcionario_origen= Funcionario_establecimiento::join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->where('funcionario_establecimiento.fe_id',$fe_id_origen)->select('funcionario_establecimiento.fe_id','fe_memorandum','fe_inicio_trabajo','fe_fin_trabajo','fe_cargo','fe_estado_laboral','fe_carga_laboral','persona.per_id','per_ci','per_ci_expedido','per_nombres','per_apellido_primero','per_apellido_segundo','per_fecha_nacimiento','per_genero','per_email','per_tipo_permanencia','per_numero_celular','per_clave_publica','per_tipo_documento','per_pais')->get();

       if($fe_id_contacto!=null)
       {
         $funcionario_contacto= Funcionario_establecimiento::join('funcionario','funcionario.fun_id','=','funcionario_establecimiento.fun_id')->join('persona','persona.per_id','=','funcionario.per_id')->where('funcionario_establecimiento.fe_id',$fe_id_contacto)->select('funcionario_establecimiento.fe_id','fe_memorandum','fe_inicio_trabajo','fe_fin_trabajo','fe_cargo','fe_estado_laboral','fe_carga_laboral','persona.per_id','per_ci','per_ci_expedido','per_nombres','per_apellido_primero','per_apellido_segundo','per_fecha_nacimiento','per_genero','per_email','per_tipo_permanencia','per_numero_celular','per_clave_publica','per_tipo_documento','per_pais')->get();
       }
        $resultado=compact('referencia','establecimiento_origen','establecimiento_destino','contrareferencia','persona_paciente','direccion','funcionario_origen','funcionario_contacto');

         return response()->json(['status'=>'ok','contrareferencia'=>$resultado],200); 

       }
/**
 * @api {put} /referencia/:bc_id modifica los campos de la boleta de contrareferencia
 * @apiVersion 0.2.0
 * @apiName PutContrareferencia
 
 * @apiParam {Number} br_id referencia unique ID.
 * @apiSuccess {Array} contrareferencia
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     { *       "status": ok,
 *       "mensaje": exito
 *     }
 *
 * @apiError 'No se encuentra una contrareferencia con ese código.'
 */
    public function update(Request $request, $bc_id)
    {
        $input = $request->all();   
        $contrareferencia=Boleta_contrareferencia::find($bc_id);
        if (!$contrareferencia)
        {
return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una contrareferencia con ese código.'])],404);
        }
        $contrareferencia->update($input);
      $contrareferencia=Boleta_contrareferencia::find($bc_id);

        return response()->json(['status'=>'ok','contrareferencia'=>$contrareferencia],200);      
    }
/**
 * @api {put} /contrareferencias_estados/:br_id realiza la edicion del campo estado contrareferencia esto para la aceptacion de una boleta de referencia.
 * @apiVersion 0.2.0
 * @apiName PutReferenciaEstado
 * @apiParam {Number} bc_id referencia unique ID.
 * @apiSuccess {Array} referencia
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     { *       "status": ok,
 *       "mensaje": exito
 *     }
 * @apiError 'No se encuentra una contrareferencia con ese código.
 */

    public function editar(Request $request, $bc_id)
    {
        $contrareferencia=Boleta_contrareferencia::find($bc_id);

        if (!$contrareferencia)
        {
    return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una contrareferencia con ese código.'])],404);
        }
        $contrareferencia->fe_id_destino=$request->fe_id_destino;
        $contrareferencia->bc_fecha_llegada=$request->bc_fecha_llegada;
        $contrareferencia->bc_hora_llegada=$request->bc_hora_llegada;
        $contrareferencia->bc_estado_contrareferencia=$request->bc_estado_contrareferencia;
        $contrareferencia->save();
         $br_id=$contrareferencia->br_id;
        $referencia=Boleta_referencia::find($br_id);
        $es_id_origen=$referencia->es_id_origen;
        $pac_id=$referencia->pac_id;
        $paciente_establecimiento=Paciente_establecimiento::where('pac_id',$pac_id)->get();
        foreach($paciente_establecimiento as $paciente)
        {  
        $es_id=$paciente->es_id;

        if($es_id==$es_id_origen)
        { 
        $pe_id=$paciente->pe_id; 
        }
        }
        $paciente_es =Paciente_establecimiento::find($pe_id);
        $paciente_es->pe_estado='ACTIVO';
        $paciente_es->save();
        $contrareferencia=compact('contrareferencia','paciente_es');
        return response()->json(['status'=>'ok','contrareferencia'=>$contrareferencia],200);       
    }
/**
 * @api {delete} /contrareferencia/:bc_id Elimina una boleta de contrareferencia
 * @apiVersion 0.2.0
 * @apiName DeleteContrareferencia
 * @apiParam {Number} bc_id referencia unique ID.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "status": ok,
 *       "mensaje": registros eliminados correctamente
 *     }
 *
 * @apiError No se encuentra una contrareferencia con ese código.
 */
    public function destroy($bc_id)
    {
 $contrareferencia = Boleta_contrareferencia::find($bc_id);
 if (!$contrareferencia)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una contrareferencia con ese código.'])],404);
        }
         $contrareferencia->delete();
        return response()->json([
            "mensaje" => "registro eliminado correctamente"
            ], 200
        );
        
    }
}
