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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leads_id');
            $table->foreign('leads_id')->references('id')->on('leads')->onDelete('cascade');
            $table->unsignedBigInteger('emp_id')->nullable();
            $table->foreign('emp_id')->references('id')->on('users')->onDelete('set null');
            $table->string('person_name');
            $table->string('mobile_number', 10);
            $table->date('next_meeting_date');
            $table->string('meeting_status', 100);
            $table->string('meeting_reason')->nullable();
            $table->text('comments');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('platform', 20)->nullable();
            $table->string('device_identifier')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
