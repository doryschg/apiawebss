<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class TipoController extends Controller
{
    
      public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }
    

    public function index()
    {
        $tipos=\awebss\Models\Tipo::all();

        return response()->json([
                "msg" => "exito",
                "tipo" => $tipos
            ], 200
        );
    }
   
    public function store(Request $request)
    {
        //
        $tipos=new \awebss\Models\Tipo();
        $tipos->tip_nombre = $request->tip_nombre;
        $tipos->tip_descripcion = $request->tip_descripcion;
        $tipos->save();

        return response()->json([
                "msg" => "exito",
                "tipo" => $tipos
            ], 200
        );
    }
    
    public function show($id)
    {
        //
         $tipos= \awebss\Modles\Tipo::find($id);

        // Si no existe ese fabricante devolvemos un error.
        if (!$tipos)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta un el tipo con ese código.'])],404);
        }

        return response()->json(['status'=>'ok','tipo'=>$tipos],200);
    }
 
    public function update(Request $request, $id)
    {
        //
         $tipos= \awebss\Models\Tipo::find($id);
        if (!$tipos)
        {
       
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta un el tipo con ese código.'])],404);
        }
        $tipos->tip_nombre = $request->tip_nombre;
        $tipos->tip_descripcion = $request->tip_descripcion;
        $tipos->save();

        return response()->json([
                "msg" => "Success",
                "tipo" => $tipos
            ], 200
        );
    }

    public function destroy($id)
    {
        $tipos= \awebss\Models\Tipo::find($id);

        if (!$tipos)
        {
      
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuenta un el tipo con ese código.'])],404);
        }

        $tipos->delete();
        return response()->json(["msg"=>"exito"],200
            );
    }
}
