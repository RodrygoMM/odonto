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
        Schema::create('units', function (Blueprint $table) {
            $table->id();

            // Cada unidade pertence a um tenant (rede / cliente)
            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nome');

            // CNPJ da unidade (matriz ou filial)
            $table->string('cnpj', 18)->unique();

            // Flag para identificar a matriz
            $table->boolean('is_matriz')->default(false);

            // Status da unidade
            $table->boolean('ativo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
