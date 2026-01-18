<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;
use App\Enums\UserTypeEnum;

class IsUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! Filament::auth()->user()?->active &&
            ! Filament::auth()->user()?->type == UserTypeEnum::ADMIN->value
        ) {
            Filament::auth()->logout();
            abort(403, "Your account not active\npleas contact with tha admin");
        }

        return $next($request);
    }
}
