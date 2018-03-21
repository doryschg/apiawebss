<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class SubsectorController extends Controller
{

      public function __construct()
    {
        $this->middleware('jwt.auth',['only' => ['store']]);
    }
    
    
    public function index()
    {
        $subsector=\awebss\Models\Subsector::all();

        return response()->json([
                "msg" => "exito",
                "subsector" => $subsector
            ], 200
        );


    }
    public function store(Request $request)
    {
         $subsector= new \awebss\Models\Subsector();
       
        $subsector->ss_nombre = $request->ss_nombre;
        $subsector->save();

         return response()->json([
                "msg" => "exito",
                "subsector" => $subsector
            ], 200
        );
    }

}
