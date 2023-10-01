<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRole
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next;
     * @param string $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!method_exists(User::class, $role)) {
            throw new \LogicException("Метод $role отсутствует у модели пользователя");
        }

        if (!Auth::check() || !Auth::user()->$role()) {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}