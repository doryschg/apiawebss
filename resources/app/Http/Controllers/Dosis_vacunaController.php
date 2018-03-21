<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Validator;
use awebss\Models\Vacuna;
use awebss\Models\Dosis_vacuna;
use JWTAuth;
use Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;

class Dosis_vacunaController extends Controller
{

     public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }

    
    public function index()
    {
         Dosis_vacuna::all();

     return response()->json(['status'=>'ok','mensaje'=>'exito','dosis_vacuna'=>$dosis_vacuna],200); 
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'vac_id' => 'required', ]);

        if ($validator->fails()) 

        {
            return $validator->errors()->all();
        } 
        $dosis_vacuna= new Dosis_vacuna();
        $dosis_vacuna->vac_id=$request->vac_id;
        $dosis_vacuna->dov_tipo=$request->dov_tipo;
        $dosis_vacuna->dov_suministro=$request->dov_suministro;
        $dosis_vacuna->dov_edad_inicio=$request->dov_edad_inicio;
        $dosis_vacuna->dov_edad_fin=$request->dov_edad_fin;
        $dosis_vacuna->dov_numero_dosis=$request->dov_numero_dosis;
        $dosis_vacuna->userid_at=JWTAuth::toUser()->id;
        $dosis_vacuna->save();

        return response()->json(['status'=>'ok','mensaje'=>'exito','dosis_vacuna'=>$dosis_vacuna],200); 


    }

 
    public function show($dov_id)
    {
        $dosis_vacuna= Dosis_vacuna::find($dov_id);

         if (!$dosis_vacuna)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una dosis vacuna con ese código.'])],404);
        }

        return response()->json(['status'=>'ok','mensaje'=>'exito','dosis_vacuna'=>$dosis_vacuna],200);  
    }

    public function update(Request $request, $dov_id)
    {

        $input = $request->all();

        $dosis_vacuna=Dosis_vacuna::find($dov_id);

         if (!$dosis_vacuna)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una dosis vacuna con ese código.'])],404);
        }

        $dosis_vacuna->update($input);

      $dosis_vacuna=Dosis_vacuna::find($dov_id);

        return response()->json(['status'=>'ok','mensaje'=>'exito','dosis_vacuna'=>$dosis_vacuna],200); 
        
    }

    public function destroy($dov_id)
    {
       $dosis_vacuna= Dosis_vacuna::find($dov_id);

         if (!$dosis_vacuna)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una dosis vacuna con ese código.'])],404);
        }

        $dosis_vacuna->delete();

        return response()->json([
            "mensaje" => "registro eliminado correctamente"
            ], 200
        ); 
    }
}
