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
        Schema::create('usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('correo')->unique();
            $table->date('Fecha_creacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.scs
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
