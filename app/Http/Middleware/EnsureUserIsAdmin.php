<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware que bloqueia o acesso a rotas exclusivas de administrador.
 * Verifica se o usuário autenticado possui tipo 'admin'.
 * Retorna 403 (Forbidden) caso contrário.
 *
 * Uso nas rotas:
 *   Route::middleware('admin')->group(function () { ... });
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Se não estiver logado ou não for admin, bloqueia
        if (!$request->user() || $request->user()->tipo !== 'admin') {
            return response()->json([
                'mensagem' => 'Acesso restrito a administradores.',
            ], 403);
        }

        return $next($request);
    }
}
