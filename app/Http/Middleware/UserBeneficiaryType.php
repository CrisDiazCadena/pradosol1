<?php

namespace App\Http\Middleware;

use App\Models\Partner;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBeneficiaryType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if(Auth::check() && Auth::user()->id==Partner::select('user_id') && Partner::select('vinculation')!='none'){
                return $next($request);
        }
        else{
            return redirect('/');
        }
    }
}
