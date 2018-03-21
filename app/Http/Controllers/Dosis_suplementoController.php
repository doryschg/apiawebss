<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use Validator;
use Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class Dosis_suplementoController extends Controller
{

      public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }


    public function index()
    {
        $dosis_suplemento= \awebss\Models\Dosis_suplemento::all();
        return response()->json(['status'=>'ok','mensaje'=>'exito','dosis_suplemento'=>$dosis_suplemento],200); 
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'sup_id' => 'required', ]);

        if ($validator->fails()) 

        {
            return $validator->errors()->all();
        } 

        $dosis_suplemento= new \awebss\Models\Dosis_suplemento();
        $dosis_suplemento->sup_id=$request->sup_id;
        $dosis_suplemento->dos_edad_inicio=$request->dos_edad_inicio;
        $dosis_suplemento->dos_edad_fin=$request->dos_edad_fin;
        $dosis_suplemento->dos_unidad_dosis=$request->dos_unidad_dosis;
        $dosis_suplemento->dos_cantidad=$request->dos_cantidad;
        $dosis_suplemento->dos_suministro=$request->dos_suministro;
        $dosis_suplemento->dos_numero_dosis=$request->dos_numero_dosis;
        $dosis_suplemento->userid_at=JWTAuth::toUser()->id;
        $dosis_suplemento->save();

        return response()->json(['status'=>'ok','mensaje'=>'exito','dosis_suplemento'=>$dosis_suplemento],200); 
    }

  
    public function show($dos_id)
    {
        $dosis_suplemento= \awebss\Models\Dosis_suplemento::find($dos_id);

         if (!$dosis_suplemento)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una dosis de suplemento con ese código.'])],404);
        }

        return response()->json(['status'=>'ok','mensaje'=>'exito','dosis_suplemento'=>$dosis_suplemento],200);  
    }

 
    public function update(Request $request, $dos_id)
    {
        
        $input = $request->all();

        $dosis_suplemento=\awebss\Models\Dosis_suplemento::find($dos_id);

         if (!$dosis_suplemento)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una dosis suplemento con ese código.'])],404);
        }

        $dosis_suplemento->update($input);

      $dosis_suplemento=\awebss\Models\Dosis_suplemento::find($dos_id);

        return response()->json(['status'=>'ok','mensaje'=>'exito','dosis_suplemento'=>$dosis_suplemento],200); 
    }

  
    public function destroy($dos_id)
    {
        $dosis_suplemento= \awebss\Models\Dosis_suplemento::find($dos_id);

         if (!$dosis_suplemento)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta una dosis suplemento con ese código.'])],404);
        }

        $dosis_suplemento->delete();

        return response()->json([
            "mensaje" => "registro eliminado correctamente"
            ], 200
        );
    }
}
