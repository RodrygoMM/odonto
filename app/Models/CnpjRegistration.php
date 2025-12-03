<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CnpjRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'unit_id',
        'cnpj',
        'razao_social',
        'nome_fantasia',
        'tipo_estabelecimento',
        'data_abertura',
        'porte',
        'cnae_principal_codigo',
        'cnae_principal_descricao',
        'cnaes_secundarios',
        'natureza_juridica_codigo',
        'natureza_juridica_descricao',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'municipio',
        'uf',
        'cep',
        'email',
        'telefone',
        'situacao_cadastral',
        'data_situacao_cadastral',
        'motivo_situacao_cadastral',
        'situacao_especial',
        'data_situacao_especial',
        'raw_payload',
    ];

    protected $casts = [
        'data_abertura'             => 'date',
        'data_situacao_cadastral'   => 'date',
        'data_situacao_especial'    => 'date',
        'cnaes_secundarios'         => 'array',
        'raw_payload'               => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
