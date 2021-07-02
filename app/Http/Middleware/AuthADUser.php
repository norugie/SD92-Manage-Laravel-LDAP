<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

class AuthADUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session('userName') && !session('userEmail'))
        {
            return redirect ('/signin');
        } else 
        {
            $viewData = [];
    
            // Check for flash errors
            if (session('error')) {
                $viewData['error'] = session('error');
                $viewData['errorDetail'] = session('errorDetail');
            }
        
            // Check for logged on user
            if (session('userName') && session('userEmail'))
            {
                $viewData['userName'] = session('userName');
                $viewData['userEmail'] = session('userEmail');
            }

            $page = explode('/', $request->path());
            $viewData['pageSection'] = $page[1];

            if(isset($page[2])) $viewData['pageSubSection'] = $page[2];

            $data = json_decode(json_encode($viewData), FALSE);

            View::share('data', $data);
        }

        return $next($request);
    }
}
