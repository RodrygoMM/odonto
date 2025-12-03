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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            // Dados básicos da rede / cliente
            $table->string('nome_fantasia');
            $table->string('razao_social')->nullable();
            $table->string('cnpj_matriz', 18)->unique();

            // Contato / cobrança
            $table->string('email_billing');

            // Licenças (quantidade, não valores em dinheiro)
            $table->unsignedInteger('licenses_total')->default(1);
            $table->unsignedInteger('licenses_used')->default(0);

            // Status do contrato
            $table->boolean('ativo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
