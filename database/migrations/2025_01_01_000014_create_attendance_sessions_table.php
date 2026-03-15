<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_subject_id')->constrained('class_subjects')->cascadeOnDelete();
            $table->foreignId('taken_by')->constrained('users')->cascadeOnDelete();
            $table->date('session_date');
            $table->time('session_time')->nullable();
            $table->string('topic')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['class_subject_id', 'session_date'], 'session_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
