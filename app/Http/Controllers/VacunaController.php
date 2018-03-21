<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;
use awebss\Http\Requests;
use Validator;
use Illuminate\Support\Str;
use awebss\Models\Vacuna;
use awebss\Models\Dosis_vacuna;
use JWTAuth;
use Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;
class VacunaController extends Controller
{
    
       public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }

    public function index()
    {
        
    $vacuna=Vacuna::all();

     return response()->json(['status'=>'ok','mensaje'=>'exito','vacuna'=>$vacuna],200); 
    }
   
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'vac_nombre' => 'required', ]);

        if ($validator->fails()) 

        {
            return $validator->errors()->all();
        } 

        $vacuna= new Vacuna();
        
        $vacuna->vac_nombre= Str::upper($request->vac_nombre);
        $vacuna->vac_cant_dosis=$request->vac_cant_dosis;
        $vacuna->vac_descripcion=$request->vac_descripcion;
        $vacuna->userid_at=JWTAuth::toUser()->id;

        $vacuna->save();

        return response()->json(['status'=>'ok','mensaje'=>'exito','vacuna'=>$vacuna],200); 

    }

     public function listar_vacuna_dosis($vac_id)
    {

        $vacuna= Vacuna::find($vac_id);

        if (!$vacuna)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una vacuna con ese código.'])],404);
        }

        $vacuna_dosis=\awebss\Models\Dosis_vacuna::where('vac_id',$vac_id)->get();

        $resultado=compact('vacuna','vacuna_dosis');

        return response()->json(['status'=>'ok','mensaje'=>'exito','vacuna_dosis'=>$resultado],200);   
    }

    public function show($vac_id)
    {

        $vacuna= Vacuna::find($vac_id);

        if (!$vacuna)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una vacuna con ese código.'])],404);
        }

        return response()->json(['status'=>'ok','mensaje'=>'exito','vacuna'=>$vacuna],200);   
    }
  
    public function update(Request $request, $vac_id)
    {
         $vacuna= Vacuna::find($vac_id);

        if (!$vacuna)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una vacuna con ese código.'])],404);
        }
        
        $vacuna->vac_nombre=$request->vac_nombre;
        $vacuna->vac_cant_dosis=$request->vac_cant_dosis;
        $vacuna->vac_descripcion=$request->vac_descripcion;
       
        $vacuna->save();

        return response()->json(['status'=>'ok','mensaje'=>'exito','vacuna'=>$vacuna],200); 
    }

    public function destroy($vac_id)
    {

        $vacuna= Vacuna::find($vac_id);

        if (!$vacuna)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una vacuna con ese código.'])],404);
        }

        $dosis_vacuna=Dosis_vacuna::where('vac_id',$vac_id)->get();

        foreach($dosis_vacuna as $dosis) {  
        $dov_id=$dosis->dov_id;
        $dosis1= Dosis_vacuna::find($dov_id);  
        $dosis1->delete();  }
        
        $vacuna->delete();

         return response()->json([
            "mensaje" => "registro eliminados correctamente"
            ], 200
        );

    }
}
