<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;
use awebss\Http\Requests;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Validator;
use awebss\Models\Establecimiento_salud;

class Establecimiento_saludController extends Controller
{

 public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','destroy','update']]);
    } 

 public function index()
    {

    $establecimiento_salud=Establecimiento_salud::select('establecimiento_salud.es_id','es_nombre','es_nivel','es_nit','es_fecha_inicio_actividad','es_zona_localidad_comuni','es_avenida_calle','es_numero','es_horas','es_inicio_atencion','es_final_atencion','es_latitud','es_longitud','es_altitud','es_codigo','es_fax','es_correo_electronico','es_direccion_web','es_fecha_creacion','es_numero_rm','red.red_id','red_nombre','municipio.mun_id','mun_nombre')->join('red','red.red_id','=','establecimiento_salud.red_id')
        ->join('municipio','municipio.mun_id','=','establecimiento_salud.mun_id')->orderBy('es_nombre')->get(); 

  return response()->json(['status'=>'ok','establecimiento'=>$establecimiento_salud],200); 
    
    }
public function listar_establecimientos_red($red_id)
    {
        $establecimiento=Establecimiento_salud::select('establecimiento_salud.es_id','tip_id','ins_id','es_nombre','es_nivel','es_nit','es_fecha_inicio_actividad','es_zona_localidad_comuni','es_avenida_calle','es_numero','es_horas','es_inicio_atencion','es_final_atencion','es_latitud','es_longitud','es_altitud','es_codigo','es_fax','es_correo_electronico','es_direccion_web','es_fecha_creacion','es_numero_rm','red.red_id','red_nombre','red_descripcion')->join('red','red.red_id','=','establecimiento_salud.red_id')->where('red.red_id','=',$red_id)->orderBy('es_nombre')->get();  
 return response()->json(['status'=>'ok','establecimiento'=>$establecimiento],200); 
    }

public function store(Request $request)
    {
$validator = Validator::make($request->all(), [
            
            'mun_id' => 'required',
            'es_nombre' => 'required',
            'red_id' => 'required',
            'tip_id' => 'required',
            'ins_id' => 'required',
            'es_nit' => 'required',  
        ]);

        if ($validator->fails()) 

        {
            return $validator->errors()->all();
        }  
$establecimientos= new Establecimiento_salud();
$establecimientos->es_nombre = $request->es_nombre;
$establecimientos->mun_id=$request->mun_id;
$establecimientos->red_id=$request->red_id;
$establecimientos->tip_id=$request->tip_id;
$establecimientos->es_nivel = $request->es_nivel;

$establecimientos->es_nit= $request->es_nit;
$establecimientos->es_fecha_inicio_actividad = $request->es_fecha_inicio_actividad;
$establecimientos->es_zona_localidad_comuni = $request->es_zona_localidad_comuni;

$establecimientos->es_avenida_calle = $request->es_avenida_calle;
$establecimientos->es_numero = $request->es_numero;
$establecimientos->es_horas = $request->es_horas;
$establecimientos->es_inicio_atencion = $request->es_inicio_atencion;
$establecimientos->es_final_atencion = $request->es_final_atencion ;

$establecimientos->es_latitud = $request->es_latitud;
$establecimientos->es_longitud = $request->es_longitud;
$establecimientos->es_altitud = $request->es_altitud;
$establecimientos->es_codigo = $request->es_codigo;
$establecimientos->es_fax = $request->es_fax;
$establecimientos->es_correo_electronico= $request->es_correo_electronico;
$establecimientos->es_direccion_web = $request->es_direccion_web;
$establecimientos->es_fecha_creacion = $request->es_fecha_creacion;
$establecimientos->es_numero_rm= $request->es_numero_rm;
$establecimientos->ins_id=$request->ins_id;
$establecimientos->save();

$telefonos= new \awebss\Models\Telefono();
$telefonos->es_id=$establecimientos->es_id;
$telefonos->te_numero=$request->te_numero;
$telefonos->save();

$imagen = new \awebss\Models\Imagen_est();
$imagen->es_id=$establecimientos->es_id;
$imagen->ie_nombre=$request->ie_nombre;
$imagen->ie_enlace=$request->ie_enlace;
$imagen->ie_tipo=$request->ie_tipo;
$imagen->save();

$result = compact('establecimientos','telefonos','imagen');
   return response()->json(['status'=>'ok',"mensaje"=>"creado exitosamente","establecimiento"=>$result], 200);

    }
public function show($es_id)
    {
        $establecimientos= Establecimiento_salud::find($es_id);

        if (!$establecimientos)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra el establecimientos con ese código.'])],404);
        }

        $mun_id=$establecimientos->mun_id;

        $municipios= \awebss\Models\Municipio::find($mun_id);

        $tip_id=$establecimientos->tip_id;

        $tipos= \awebss\Models\Tipo::find($tip_id);

        $red_id=$establecimientos->red_id;

        $reds= \awebss\Models\Red::find($red_id);

        $imagen= \awebss\Models\Imagen_est::where('es_id',$es_id)->get()->first();

        if (!$imagen)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra la imagen asociada al establecimietno'])],404);
        }

        $ins_id=$establecimientos->ins_id;

        $institucion=\awebss\Models\Institucion::find($ins_id);

        $ss_id=$institucion->ss_id;

        $subsector=\awebss\Models\Subsector::find($ss_id);

        $ie_id=$imagen->ie_id;

        $imagenes=\awebss\Models\Imagen_est::find($ie_id);

        $institucion=$institucion->toArray();

        $telefonos=\awebss\Models\Telefono::where('es_id',$es_id)->get();

        //$establecimientos=$establecimientos->toArray();

        $establecimiento=compact('establecimientos','municipios','tipos','reds','institucion','subsector','imagenes','telefonos');
       
return response()->json(['status'=>'ok','establecimiento'=>$establecimiento],200);
    }

    public function update(Request $request, $es_id)
    {

$establecimientos= Establecimiento_salud::find($es_id);

if (!$establecimientos)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un establecimiento con ese código.'])],404);
        }

$establecimientos->es_nombre = $request->es_nombre;
$establecimientos->mun_id=$request->mun_id;
$establecimientos->red_id=$request->red_id;
$establecimientos->tip_id=$request->tip_id;
$establecimientos->es_nivel = $request->es_nivel;

$establecimientos->es_nit= $request->es_nit;
$establecimientos->es_fecha_inicio_actividad = $request->es_fecha_inicio_actividad;
$establecimientos->es_zona_localidad_comuni = $request->es_zona_localidad_comuni;

$establecimientos->es_avenida_calle = $request->es_avenida_calle;
$establecimientos->es_numero = $request->es_numero;
$establecimientos->es_horas = $request->es_horas;
$establecimientos->es_inicio_atencion = $request->es_inicio_atencion;
$establecimientos->es_final_atencion = $request->es_final_atencion ;

$establecimientos->es_latitud = $request->es_latitud;
$establecimientos->es_longitud = $request->es_longitud;
$establecimientos->es_altitud = $request->es_altitud;
$establecimientos->es_codigo = $request->es_codigo;
$establecimientos->es_fax = $request->es_fax;
$establecimientos->es_correo_electronico= $request->es_correo_electronico;
$establecimientos->es_direccion_web = $request->es_direccion_web;
$establecimientos->es_fecha_creacion = $request->es_fecha_creacion;
$establecimientos->es_numero_rm= $request->es_numero_rm;
$establecimientos->ins_id=$request->ins_id;

$establecimientos->save();

$telefono = \awebss\Models\Telefono::where('es_id', $es_id)->get()->first();

$te_id=$telefono->te_id;

$telefonos= \awebss\Models\Telefono::find($te_id);
$telefonos->te_numero=$request->te_numero;
$telefonos->save();

$imagenes = \awebss\Models\Imagen_est::where('es_id', $es_id)->get()->first();

$ie_id=$imagenes->ie_id;

$imagen = \awebss\Models\Imagen_est::find($ie_id);
//$imagen->es_id=$establecimientos->es_id;
$imagen->ie_nombre=$request->ie_nombre;
$imagen->ie_enlace=$request->ie_enlace;
$imagen->ie_tipo=$request->ie_tipo;
$imagen->save();

   $result = compact('establecimientos','telefonos','imagen');
   return response()->json(['status'=>'ok',"mensaje"=>"creado exitosamente","establecimiento"=>$result], 200);
        
    }

    public function destroy($es_id)
    {
        $establecimientos = Establecimiento_salud::find($es_id);

        if (!$establecimientos)
        {
        
        return response()->json(["mensaje"=>"no se encuentra un establecimiento con ese codigo"]);
        
        }

        $telefono = \awebss\Models\Telefono::where('es_id', $es_id)->get()->first();

        $te_id=$telefono->te_id;

        $telefonos = \awebss\Models\Telefono::find($te_id);

        $telefonos->delete();

        $imagen = \awebss\Models\Imagen_est::where('es_id', $es_id)->get()->first();

        $ie_id=$imagen->ie_id;

        $imagenes = \awebss\Models\Imagen_est::find($ie_id);
        
        $imagenes->delete();

        $establecimientos->delete();

        return response()->json([

            "mensaje" => "eliminado E.S. telefono e imagen"
            ], 200
        );
    }
}
