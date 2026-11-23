<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            
            // Basic Info
            $table->string('title');
            $table->longText('description')->nullable();

            // Relations
            $table->foreignId('state_id')
                ->constrained('states')
                ->cascadeOnDelete();

            $table->foreignId('city_id')
                ->constrained('cities')
                ->cascadeOnDelete();

            $table->foreignId('property_type_id')
                ->constrained('property_types')
                ->cascadeOnDelete();

            // Extra Details
            $table->string('tags')->nullable(); // comma separated
            $table->string('location')->nullable();

            // Pricing & Date
            $table->decimal('price', 15, 2)->nullable();
            $table->date('property_date')->nullable();

            // Images (JSON for multiple images)
            $table->json('images')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
