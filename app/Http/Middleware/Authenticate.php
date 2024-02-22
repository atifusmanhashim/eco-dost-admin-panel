<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        $status_code=401;
        $data=[
            "response"=>[
                    "msg"=>"Unauthenticated.",
                    "success"=>false,
                    "data"=>[]
                ]
           
            ];
        return response()->json($data, $status_code); 

        // if (! $request->expectsJson()) {
        //     return route('login');
        // }
    }
    protected function unauthenticated($request, array $guards)
    {
        $status_code=401;
        $data=[
            "response"=>[
                    "msg"=>"Unauthenticated.",
                    "success"=>false,
                    "data"=>[]
                ]
           
            ];
        return response()->json($data, $status_code); 
       // abort(response()->json(['error' => 'Unauthenticated.'], 401));
    }

}
