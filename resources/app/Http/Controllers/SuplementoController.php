<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;

use Illuminate\Support\Str;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class SuplementoController extends Controller
{
  public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }
    
    
    public function index()
    {
        $suplemento= \awebss\Models\Suplemento::all();
        return response()->json(['status'=>'ok','mensaje'=>'exito','suplemento'=>$suplemento],200);   
    }
    public function store(Request $request)
    {
        $suplemento= new \awebss\Models\Suplemento();
        $suplemento->sup_nombre=Str::upper($request->sup_nombre);
        $suplemento->sup_tipo_suplemento=Str::upper($request->sup_tipo_suplemento);
        $suplemento->sup_descripcion=$request->sup_descripcion;
        $suplemento->sup_cant_dosis=$request->sup_cant_dosis;
        $suplemento->userid_at=JWTAuth::toUser()->id;
        $suplemento->save();

        return response()->json(['status'=>'ok','mensaje'=>'exito','suplemento'=>$suplemento],200); 
    }

    public function show($sup_id)
    {
        $suplemento= \awebss\Models\Suplemento::find($sup_id);

         if (!$suplemento)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta un suplemento con ese código.'])],404);
        }

        return response()->json(['status'=>'ok','mensaje'=>'exito','suplemento'=>$suplemento],200);  
    }

     public function listar_suplemento_dosis($sup_id)
    {

        $suplemento= \awebss\Models\Suplemento::find($sup_id);

        if (!$suplemento)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta un suplemento con ese código.'])],404);
        }

        $suplemento_dosis=\awebss\Models\Dosis_suplemento::where('sup_id',$sup_id)->get();

        $resultado=compact('suplemento','suplemento_dosis');

        return response()->json(['status'=>'ok','mensaje'=>'exito','suplemento_dosis'=>$resultado],200);   
    }

    public function update(Request $request, $sup_id)
    {
         $input = $request->all();

        $suplemento = \awebss\Models\Suplemento::find($sup_id);

         if (!$suplemento)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta un suplemento con ese código.'])],404);
        }

        $suplemento->update($input);

        $suplemento = \awebss\Models\Suplemento::find($sup_id);

        return response()->json(['status'=>'ok','mensaje'=>'exito','dosis_vacuna'=>$suplemento],200); 

    }

        public function destroy($sup_id)
    {
        $suplemento= \awebss\Models\Suplemento::find($sup_id);

        if (!$suplemento)
        {

            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta un suplemento con ese código.'])],404);
        }

        $dosis_suplemento=\awebss\Models\Dosis_suplemento::where('sup_id',$sup_id)->get();

        foreach($dosis_suplemento as $dosis) {  

        $dos_id=$dosis->dos_id;
        $dosis1= \awebss\Models\Dosis_suplemento::find($dos_id);  
        $dosis1->delete();  }
        
        $suplemento->delete();

         return response()->json([
            "mensaje" => "registro eliminados correctamente suplemento y dosis"
            ], 200
        );
    }
}
