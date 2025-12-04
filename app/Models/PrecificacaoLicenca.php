<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrecificacaoLicenca extends Model
{
    use HasFactory;

    protected $table = 'precificacao_licencas';

    protected $fillable = [
        'id_origem',
        'descricao',
        'ativa',
        'moeda',
        'valor_combo_inicial_mensal',
        'valor_licenca_medico_adicional_mensal',
        'valor_licenca_recepcionista_adicional_mensal',
        'competencia_inicio',
        'competencia_fim',
        'data_inicio_comunicacao_reajuste',
        'observacao_interna',
        'motivo_alteracao',
        'criado_por_usuario_id',
        'atualizado_por_usuario_id',
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'competencia_inicio' => 'date',
        'competencia_fim' => 'date',
        'data_inicio_comunicacao_reajuste' => 'date',
    ];

    /**
     * Versão original (raiz) deste conjunto de precificação.
     * - Se este registro for o original, retorna null.
     */
    public function origem()
    {
        return $this->belongsTo(PrecificacaoLicenca::class, 'id_origem');
    }

    /**
     * Todas as versões que pertencem ao mesmo conjunto (mesma origem).
     * Útil pra auditoria.
     */
    public function historicoDoConjunto()
    {
        $origemId = $this->id_origem ?? $this->id;

        return self::where('id', $origemId)
            ->orWhere('id_origem', $origemId)
            ->orderBy('id');
    }
}
