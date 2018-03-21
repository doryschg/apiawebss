<?php

namespace awebss\Http\Controllers;

use Illuminate\Http\Request;

use awebss\Http\Requests;

class CargoController extends Controller
{

    public function index()
    {
        $cargo=\awebss\Models\Cargo::all();

         return response()->json([
                "msg" => "exito",
          "cargo" => $cargo
            ], 200
        );  
    }

}
