<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'nome_fantasia',
        'razao_social',
        'cnpj_matriz',
        'email_billing',
        'licenses_total',
        'licenses_used',
        'ativo',
    ];

    /**
     * Casts de tipos.
     */
    protected $casts = [
        'licenses_total' => 'integer',
        'licenses_used'  => 'integer',
        'ativo'          => 'boolean',
    ];

    /**
     * Um tenant possui várias unidades (matriz + filiais).
     */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Um tenant possui vários usuários.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
