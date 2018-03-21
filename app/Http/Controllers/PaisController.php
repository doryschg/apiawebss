<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;

class PaisController extends Controller
{
      
    public function index()
    {
        $pais=\awebss\Models\Pais::all();

         return response()->json(['status'=>'ok','mensaje'=>'exito','pais'=>$pais],200); 
    }
}
