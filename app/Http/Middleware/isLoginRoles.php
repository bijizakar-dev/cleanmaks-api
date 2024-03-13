<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isLoginRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        if(!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        if(!empty($user)) {
            $m_user = new User;
            $data_user = $m_user->getUserEmployee($user->id);

            if ($user->role == $roles) {
                return $next($request);
            }
        }

        return redirect('login')->with('error','Maaf Anda Tidak Memiliki Akses');

    }
}
