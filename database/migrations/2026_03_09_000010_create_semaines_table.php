<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semaines', function (Blueprint $table) {
            $table->id();
            $table->integer('annee');
            $table->integer('numero_semaine');
            $table->date('date_debut'); // Lundi
            $table->date('date_fin');   // Dimanche
            $table->timestamps();
            $table->unique(['annee', 'numero_semaine']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semaines');
    }
};
