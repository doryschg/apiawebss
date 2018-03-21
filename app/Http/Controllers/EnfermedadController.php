<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Validator;
use Illuminate\Support\Str;
use JWTAuth;
use Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;

class EnfermedadController extends Controller
{
      public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }
    

   public function index()
    {
        $enfermedad=\awebss\Models\Enfermedad::orderBy('enf_nombre')->get();

     return response()->json(['status'=>'ok','mensaje'=>'exito','enfermedad'=>$enfermedad],200); 

    }

    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
            
            'enf_nombre' => 'required',
            'enf_causas' => 'required',
            'enf_consecuencia' => 'required',
            'enf_descripcion' => 'required',
            'enf_prevencion' => 'required',

        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }
        


        $enfermedad= new \awebss\Models\Enfermedad();
        $enfermedad->enf_nombre=Str::upper($request->enf_nombre);
        $enfermedad->enf_causas=$request->enf_causas;
        $enfermedad->enf_consecuencia=$request->enf_consecuencia;
        $enfermedad->enf_descripcion=$request->enf_descripcion;
        $enfermedad->enf_prevencion=$request->enf_prevencion;
        $enfermedad->userid_at=JWTAuth::toUser()->id;

        $enfermedad->save();
        
        $imagen_enfermedad = new \awebss\Models\Imagen_enfermedad();
        $imagen_enfermedad ->enf_id=$enfermedad->enf_id;
        $imagen_enfermedad ->ie_ruta=$request->ie_ruta;
        $imagen_enfermedad ->ie_nombre=$request->ie_nombre;
        $imagen_enfermedad->userid_at=JWTAuth::toUser()->id;
        $imagen_enfermedad ->save();

        $resultado=compact('enfermedad','imagen_enfermedad');

        return response()->json(['status'=>'ok','mensaje'=>'exito','enfermedad'=>$resultado],200); 
    
    }

    public function show($enf_id)
    {
        $enfermedad= \awebss\Models\Enfermedad::find($enf_id);

         if (!$enfermedad)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una enfermedad con ese código.'])],404);
        }

    $imagen_enfermedad=\awebss\Models\Imagen_enfermedad::where('enf_id',$enf_id)->get();

$resultado=compact('enfermedad','imagen_enfermedad');

        return response()->json(['status'=>'ok','mensaje'=>'exito','enfermedad'=>$resultado],200);  
    }

    public function update(Request $request, $enf_id)
    {

     $enfermedad= \awebss\Models\Enfermedad::find($enf_id);

         if (!$enfermedad)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una enfermedad con ese código.'])],404);
        }
        $enfermedad->enf_nombre=$request->enf_nombre;
        $enfermedad->enf_causas=$request->enf_causas;
        $enfermedad->enf_consecuencia=$request->enf_consecuencia;
        $enfermedad->enf_descripcion=$request->enf_descripcion;
        $enfermedad->enf_prevencion=$request->enf_prevencion;
        $enfermedad->save();

        return response()->json(['status'=>'ok','mensaje'=>'exito','enfermedad'=>$enfermedad],200); 
    }

    public function destroy($enf_id)
    {

        $enfermedad= \awebss\Models\Enfermedad::find($enf_id);

         if (!$enfermedad)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una enfermedad con ese código.'])],404);
        }

        $imagen= \awebss\Models\Imagen_enfermedad::where('enf_id',$enf_id)->first();

        $enfermedad->delete();
        $imagen->delete();

        return response()->json([
            "mensaje" => "registro eliminado correctamente"
            ], 200
        );

        
    }
}
