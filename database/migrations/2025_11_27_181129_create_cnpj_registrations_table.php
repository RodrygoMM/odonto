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
        Schema::create('cnpj_registrations', function (Blueprint $table) {
            $table->id();

            // Amarra ao tenant e à unidade (matriz/filial)
            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('unit_id')
                ->nullable()
                ->constrained('units')
                ->cascadeOnDelete();

            // Dados básicos do CNPJ
            $table->string('cnpj', 18)->index();
            $table->string('razao_social')->nullable();
            $table->string('nome_fantasia')->nullable();
            $table->string('tipo_estabelecimento')->nullable(); // MATRIZ / FILIAL (descrição oficial)
            $table->date('data_abertura')->nullable();
            $table->string('porte')->nullable();

            // CNAE principal e secundários
            $table->string('cnae_principal_codigo')->nullable();
            $table->string('cnae_principal_descricao')->nullable();
            $table->json('cnaes_secundarios')->nullable();

            // Natureza jurídica
            $table->string('natureza_juridica_codigo')->nullable();
            $table->string('natureza_juridica_descricao')->nullable();

            // Endereço
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('municipio')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep', 20)->nullable();

            // Contato
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();

            // Situação cadastral
            $table->string('situacao_cadastral')->nullable();
            $table->date('data_situacao_cadastral')->nullable();
            $table->string('motivo_situacao_cadastral')->nullable();

            // Situação especial
            $table->string('situacao_especial')->nullable();
            $table->date('data_situacao_especial')->nullable();

            // Payload cru da API (ótimo pra IA no futuro)
            $table->json('raw_payload')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cnpj_registrations');
    }
};
