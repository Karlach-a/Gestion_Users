<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RefreshTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado
        if ($request->user()) {
            $token = $request->user()->currentAccessToken();

            // Calcular si el token está por expirar (por ejemplo, menos de 1 minuto)
            $expiresAt = $token->created_at->addMinutes(config('sanctum.expiration'));
            if (Carbon::now()->diffInMinutes($expiresAt) <= 1) {
                // Revocar el token actual
                $token->delete();

                // Generar un nuevo token
                $newToken = $request->user()->createToken('auth_token')->plainTextToken;

                // Adjuntar el nuevo token a la respuesta
                $response = $next($request);
                $response->headers->set('New-Access-Token', $newToken);

                return $response;
            }
        }

        return $next($request);
    }
}