<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Models\CnpjRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TenantCleanupController extends Controller
{
    /**
     * Lista todos os tenants (CNPJs matriz) para limpeza em ambiente de desenvolvimento.
     *
     * Não exige login (rota pública), mas é bloqueado em produção.
     */
    public function index(Request $request): View
    {
        // Nunca permitir em produção
        if (app()->environment('production')) {
            abort(403, 'Painel de exclusão de CNPJs matriz não está disponível em produção.');
        }

        // Lista todos os tenants, mais recentes primeiro
        $tenants = Tenant::orderByDesc('created_at')->get();

        return view('dev.tenants.index', compact('tenants'));
    }

    /**
     * Exclui um tenant inteiro (CNPJ matriz) e tudo ligado a ele.
     *
     * Fluxo em cascata começando pela tabela users:
     *  - apaga usuários do tenant
     *  - apaga registros fiscais (cnpj_registrations)
     *  - apaga unidades (units)
     *  - apaga o próprio tenant
     *
     * Apenas para ambiente de desenvolvimento. Rota aberta (sem login).
     */
    public function destroy(Request $request, Tenant $tenant): RedirectResponse
    {
        if (app()->environment('production')) {
            abort(403, 'Exclusão de CNPJs matriz não é permitida em produção.');
        }

        DB::transaction(function () use ($tenant) {
            // 1) Apaga usuários ligados ao tenant (tabela users)
            User::where('tenant_id', $tenant->id)->delete();

            // 2) Apaga registros fiscais ligados ao tenant
            CnpjRegistration::where('tenant_id', $tenant->id)->delete();

            // 3) Apaga unidades ligadas ao tenant
            Unit::where('tenant_id', $tenant->id)->delete();

            // 4) Apaga o próprio tenant
            $tenant->delete();
        });

        return redirect()
            ->route('dev.tenants.index')
            ->with('status', 'Tenant (CNPJ matriz, usuários, unidades e CNPJs) excluído com sucesso.');
    }
}
