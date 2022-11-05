<?php

namespace App\Http\Middleware;

use App\Models\Partner;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAccessTypeClient
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
        if(Auth::check() && Auth::user()->id==Partner::select('user_id')){
            return $next($request);
        }
        else{
            return redirect('/');
        }
        /*
        $user = DB::table('users')->select('type')->where('email',$request->email)->first();
        if ($user && $user->type === 'client')
        {
            return $next($request);
        }
        return response()->json([
            'res' => false,
            'msg' =>"No tiene provilegios para acceder client"
        ]);
        */
    }

}
