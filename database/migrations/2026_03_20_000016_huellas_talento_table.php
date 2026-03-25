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
        Schema::create('huellas_talento', function (Blueprint $table){
            $table->id();

            $table->string('ngsi_ld_id');
            $table->json('payload');

            $table->foreignId('estudiante_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('ecosistema_laboral_id')
                ->constrained('ecosistemas_laborales')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->timestamp('generada_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('huellas_talento');
    }
};
