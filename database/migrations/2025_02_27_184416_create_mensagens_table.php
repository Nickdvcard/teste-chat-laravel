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
        Schema::create('mensagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId("conversa_id")->constrained();
            $table->unsignedBigInteger('remetente_id');
            $table->foreign('remetente_id')->references('id')->on('users');
            $table->unsignedBigInteger('destinatario_id');
            $table->foreign('destinatario_id')->references('id')->on('users');
            $table->timestamp('lido_em')->nullable();

            //açõeses de deletar
            $table->timestamp('destinatario_deletado_em')->nullable();
            $table->timestamp('remetente_deletado_em')->nullable();

            $table->text('corpo')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensagens');
    }
};
