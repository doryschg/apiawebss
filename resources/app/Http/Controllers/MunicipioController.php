<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use JWTAuth;
use Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;

class MunicipioController extends Controller
{

     public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }
    
    public function index()
    {
        
    $municipios = \awebss\Models\Municipio::orderBy('mun_nombre')->get();

    return response()->json(['status'=>'ok',"msg"=>"exito",'municipio'=>$municipios], 200);

    }

    public function store(Request $request)
    {
        $municipios= new \awebss\Models\Municipio();
        $municipios->pro_id=$request->pro_id;
        $municipios->reg_id=$request->reg_id;
        $municipios->mun_nombre = $request->mun_nombre;
        $municipios->pro_cod_sice=$request->pro_cod_sice;
        $municipios->mun_cod_sice=$request->mun_cod_sice;
        $municipios->save();

    return response()->json(['status'=>'ok','municipio'=>$municipios],200);
        
    }
    
    public function show($mun_id)
    {
        
         $municipios= \awebss\Models\Municipio::find($mun_id);

    
        if (!$municipios)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un municipio con ese código.'])],404);
        }

        return response()->json(['status'=>'ok','municipio'=>$municipios],200);
    }

    public function update(Request $request, $mun_id)
    {
        
         $input = $request->all();
    $municipios= \awebss\Models\Municipio::find($mun_id);

    if (!$municipios)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un municipio con ese código.'])],404);
        }
    
        $municipios->update($input);

       $municipios= \awebss\Models\Municipio::find($mun_id);

        return response()->json(["msg" => "exito","municipio" => $municipios
            ], 200
        );
    }


    public function destroy($mun_id)
    {

    $municipios= \awebss\Models\Municipio::find($mun_id);
     if (!$municipios)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un municipio con ese código.'])],404);
        }

    $municipios->delete();

         return response()->json(["msg"=>"registros eliminados correctamente"],200
            );
    }
}
