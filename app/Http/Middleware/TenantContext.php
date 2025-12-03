<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantContext
{
    /**
     * Handle an incoming request.
     *
     * Este middleware garante que, quando o usuário estiver autenticado,
     * o contexto de tenant/unidade fique disponível na sessão.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Garante que tenant_id e unit_id da sessão sigam o usuário logado
            if ($user->tenant_id && session('tenant_id') !== $user->tenant_id) {
                session(['tenant_id' => $user->tenant_id]);
            }

            if ($user->unit_id && session('unit_id') !== $user->unit_id) {
                session(['unit_id' => $user->unit_id]);
            }

            // Se depois você tiver coluna "role" em users, pode sincronizar aqui também:
            // if ($user->role && session('role') !== $user->role) {
            //     session(['role' => $user->role]);
            // }
        } else {
            // Se não estiver autenticado, opcionalmente limpa o contexto
            session()->forget(['tenant_id', 'unit_id']);
            // session()->forget(['role']); // quando tiver role
        }

        return $next($request);
    }
}
