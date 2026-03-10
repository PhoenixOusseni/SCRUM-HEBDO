<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->foreignId('semaine_id')->constrained('semaines')->cascadeOnDelete();
            $table->string('type'); // 'courante' | 'suivante'
            $table->text('description');
            $table->string('statut')->nullable(); // fait, en_cours, reporte, bloque, a_mettre_a_jour
            $table->text('raison')->nullable();   // obligatoire si statut != fait
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activites');
    }
};
