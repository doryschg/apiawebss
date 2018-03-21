<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RedController extends Controller
{
    
      public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store','update','destroy']]);
    }
    

    public function index()
    {

   $red = \awebss\Models\Red::all();

return response()->json(['status'=>'ok',"msg"=>"sucess",'red'=>$red], 200);

    }
  
    public function store(Request $request)
    {
        //
        $red= new \awebss\Models\Red();
       
        $red->red_nombre = $request->red_nombre;
        $red->red_descripcion = $request->red_descripcion;
        $red->save();

         return response()->json([
                "msg" => "exito",
                "red" => $red
            ], 200
        );
    }

    public function show($red_id)
    {
        //
        $red= \awebss\Models\Red::find($red_id);

        if (!$red)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una persona con ese código.'])],404);
        }

        return response()->json(['status'=>'ok','red'=>$red],200);

    }

    public function update(Request $request, $id)
    {
        
        $red= \awebss\Models\Red::find($id);
         if (!$red)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una persona con ese código.'])],404);
        }
        $red->red_nombre = $request->red_nombre;
        $red->red_descripcion = $request->red_descripcion;
        $red->save();

        return response()->json([
                "msg" => "Success",
                "red" => $red
            ], 200
        );
    }

    public function destroy($id)
    {
        
        $red= \awebss\Models\Red::find($id);

         if (!$red)
        {
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una persona con ese código.'])],404);
        }
        $red->delete();
        return response()->json(["msg"=>"registros eliminados correctamente"],200
            );
    }
}
