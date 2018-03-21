<?php

namespace awebss\Http\Middleware;

use Closure;

class Cors
{
    
    public function handle($request, Closure $next)
    {
        return $next($request);
        ->header('Access-Control-Allow-Origin', '*')  //header('Access-Control-Allow-Origin: https://sesar.sedeslapaz.gob.bo');
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');

        

           // ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

}
