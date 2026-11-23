<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_emp_id')->nullable(); // who is following up
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('project_name')->nullable();
            $table->string('full_name');
            $table->string('email');
            $table->string('mobile');
            $table->string('property_type');
            $table->string('property_status');
            $table->text('property_details')->nullable();
            $table->string('area_name');
            $table->decimal('budget_min', 15, 2);
            $table->decimal('budget_max', 15, 2);
            $table->string('lead_source');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
