<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TenantCleanupController;
use App\Http\Controllers\PrecificacaoLicencaController;
use App\Http\Controllers\InviteUserController; // <-- ADICIONADO
use Illuminate\Support\Facades\Route;

// Página inicial
Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {

    /**
     * Perfil do usuário
     */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * Rede do cliente — Unidades (matriz + filiais)
     */
    Route::prefix('unidades')->name('units.')->group(function () {
        Route::get('/', [UnitController::class, 'index'])->name('index');
        Route::get('/criar', [UnitController::class, 'create'])->name('create');
        Route::post('/', [UnitController::class, 'store'])->name('store');

        Route::get('/{unit}', [UnitController::class, 'show'])->name('show');
        Route::get('/{unit}/editar', [UnitController::class, 'edit'])->name('edit');
        Route::put('/{unit}', [UnitController::class, 'update'])->name('update');
        Route::delete('/{unit}', [UnitController::class, 'destroy'])->name('destroy');
    });

    /**
     * Convite de novos usuários (pré-cadastro de colaboradores)
     *
     * - Matriz: pode escolher qualquer unidade (CNPJ) do tenant
     * - Filial: sempre vincula à própria unidade (CNPJ travado)
     */
    Route::prefix('usuarios')->name('users.')->group(function () {
        // Formulário de convite
        Route::get('/convidar', [InviteUserController::class, 'create'])
            ->name('invite.create');

        // Processa o envio do convite / pré-cadastro
        Route::post('/convidar', [InviteUserController::class, 'store'])
            ->name('invite.store');
    });

    /**
     * Precificação de licenças
     */
    Route::resource('precificacao-licencas', PrecificacaoLicencaController::class)
        ->except(['show', 'destroy']);
});

// Painel DEV — limpeza total de tenants (perigoso, bloquear em produção)
Route::prefix('dev')->name('dev.')->group(function () {
    Route::get('/tenants', [TenantCleanupController::class, 'index'])->name('tenants.index');
    Route::delete('/tenants/{tenant}', [TenantCleanupController::class, 'destroy'])->name('tenants.destroy');
});

require __DIR__ . '/auth.php';
