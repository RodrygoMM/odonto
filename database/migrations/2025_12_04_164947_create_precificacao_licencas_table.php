<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('precificacao_licencas', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Ligação de rastreabilidade:
            // - null = registro original
            // - id do original = registros criados por edição
            $table->unsignedBigInteger('id_origem')->nullable();

            // Descrição da versão da tabela de preço
            $table->string('descricao', 150);

            // Flag de controle de uso da versão (não excluir registros, apenas inativar)
            $table->boolean('ativa')->default(true);

            // Moeda utilizada (fixo BRL, mas deixamos flexível)
            $table->string('moeda', 3)->default('BRL');

            // Valores mensais em reais (sempre BRL)
            $table->decimal('valor_combo_inicial_mensal', 10, 2);
            $table->decimal('valor_licenca_medico_adicional_mensal', 10, 2);
            $table->decimal('valor_licenca_recepcionista_adicional_mensal', 10, 2);

            // Vigência da tabela (competência)
            $table->date('competencia_inicio');          // Ex.: 2025-01-01
            $table->date('competencia_fim')->nullable(); // Nulo enquanto estiver vigente

            // Quando começamos a avisar o cliente sobre o reajuste
            $table->date('data_inicio_comunicacao_reajuste')->nullable();

            // Observações internas sobre essa versão de preços
            $table->text('observacao_interna')->nullable();

            // Motivo de alteração, para auditoria rápida
            $table->string('motivo_alteracao', 255)->nullable();

            // Auditoria de usuário
            $table->unsignedBigInteger('criado_por_usuario_id')->nullable();
            $table->unsignedBigInteger('atualizado_por_usuario_id')->nullable();

            $table->timestamps();

            // Índices úteis
            $table->index('ativa');
            $table->index('competencia_inicio');
            $table->index('competencia_fim');
            $table->index('id_origem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precificacao_licencas');
    }
};
