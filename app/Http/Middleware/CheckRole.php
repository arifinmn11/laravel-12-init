<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): BaseResponse
    {
        if (!$request->user()) {
            throw new \Illuminate\Auth\AuthenticationException('Unauthenticated.');
        }

        if (!$request->user()->hasAnyRole($roles)) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException(
                'Unauthorized. Required roles: ' . implode(', ', $roles)
            );
        }

        return $next($request);
    }
}
