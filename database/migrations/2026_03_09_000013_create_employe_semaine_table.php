<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employe_semaine', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->foreignId('semaine_id')->constrained('semaines')->cascadeOnDelete();
            $table->text('obstacles')->nullable();
            $table->timestamps();
            $table->unique(['employe_id', 'semaine_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employe_semaine');
    }
};
