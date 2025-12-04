<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TenantCleanupController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrecificacaoLicencaController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rede do cliente: unidades (matriz + filiais)
    Route::get('/unidades', [UnitController::class, 'index'])->name('units.index');
    Route::get('/unidades/criar', [UnitController::class, 'create'])->name('units.create');
    Route::post('/unidades', [UnitController::class, 'store'])->name('units.store');
    Route::get('/unidades/{unit}', [UnitController::class, 'show'])->name('units.show');
    Route::get('/unidades/{unit}/editar', [UnitController::class, 'edit'])->name('units.edit');
    Route::put('/unidades/{unit}', [UnitController::class, 'update'])->name('units.update');
    Route::delete('/unidades/{unit}', [UnitController::class, 'destroy'])->name('units.destroy');
});

// Painel DEV de limpeza total de tenants (sem login, mas bloqueado em produção)
Route::get('/dev/tenants', [TenantCleanupController::class, 'index'])->name('dev.tenants.index');
Route::delete('/dev/tenants/{tenant}', [TenantCleanupController::class, 'destroy'])->name('dev.tenants.destroy');








// ...

Route::middleware(['auth'])->group(function () {
    Route::resource('precificacao-licencas', PrecificacaoLicencaController::class)
        ->except(['show', 'destroy']);
});

require __DIR__ . '/auth.php';
