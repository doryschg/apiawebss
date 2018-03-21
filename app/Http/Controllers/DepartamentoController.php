<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;

class DepartamentoController extends Controller
{

      public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }
    public function index()
    {
    
         $departamentos = \awebss\Models\Departamento::all();

    return response()->json(['status'=>'ok',"msg"=>"exito",'departamento'=>$departamentos], 200);
    }

    public function store(Request $request)
    {
        
        $departamentos = new \awebss\Models\Departamento();
        $departamentos->dep_nombre = $request->dep_nombre;
        $departamentos->dep_abreviacion = $request->dep_abreviacion;
        $departamentos->save();
         return response()->json([
                "msg" => "exito",
                "dep_id" => $departamentos->dep_id
            ], 200
        );


    }
    public function show($dep_id)
    {
    	$departamentos = \awebss\Models\Departamento::find($dep_id);

         if (!$departamentos)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un departamento con ese código.'])],404);
        }

        
    	return response()->json([
    		"msg" => "exito",
    		"departamento"=>$departamentos
    		], 200
    	);
    }
    public function update(Request $request, $dep_id)
    {
    	$departamentos= \awebss\Models\Departamento::find($dep_id);


        if (!$departamentos)
        {
// Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un departamento con ese código.'])],404);
        }


    	$departamentos->dep_nombre = $request->dep_nombre;
        $departamentos->dep_abreviacion = $request->dep_abreviacion;
        $departamentos->save();
        return response()->json([
        		"mensaje" => "exito"
        	], 200
        );
    }

    public function destroy($dep_id)
    {
    	$departamentos = \awebss\Models\Departamento::find($dep_id);

        if (!$departamentos)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un departamento con ese código.'])],404);
        }


    	$deparamentos->delete();

    	return response()->json([
    		"msg" => "exito"
    		], 200
    	);
    }
}
