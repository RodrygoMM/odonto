<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'nome',
        'cnpj',
        'is_matriz',
        'ativo',
    ];

    protected $casts = [
        'is_matriz' => 'boolean',
        'ativo'     => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Registro fiscal (espelho do CNPJ) desta unidade.
     */
    public function cnpjRegistration(): HasOne
    {
        return $this->hasOne(CnpjRegistration::class);
    }
}
