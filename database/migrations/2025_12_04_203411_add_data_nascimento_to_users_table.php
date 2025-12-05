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
        Schema::table('users', function (Blueprint $table) {
            // data_nascimento depois do cpf (ajuste se a ordem for diferente aí)
            $table->date('data_nascimento')->nullable()->after('cpf');

            // usuário ativo? (padrão: true)
            $table->boolean('ativo')->default(true)->after('data_nascimento');

            // já monetizado? (padrão: false)
            $table->boolean('monetizado')->default(false)->after('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['data_nascimento', 'ativo', 'monetizado']);
        });
    }
};
