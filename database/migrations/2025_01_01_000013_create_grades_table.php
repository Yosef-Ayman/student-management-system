<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('class_subject_id')->constrained('class_subjects')->cascadeOnDelete();
            $table->foreignId('graded_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('marks_obtained', 5, 2);
            $table->decimal('total_marks', 5, 2);
            $table->string('grade_role', 12)->virtualAs(
                "CASE
                    WHEN (marks_obtained / total_marks * 100) >= 95 THEN 'Excellent'
                    WHEN (marks_obtained / total_marks * 100) >= 90 THEN 'Very Good'
                    WHEN (marks_obtained / total_marks * 100) >= 80 THEN 'Good'
                    WHEN (marks_obtained / total_marks * 100) >= 70 THEN 'Average Fair'
                    WHEN (marks_obtained / total_marks * 100) >= 60 THEN 'Pass'
                    ELSE 'Failure'
                END"
            );
            $table->boolean('is_absent')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamp('graded_at')->useCurrent();
            $table->timestamps();

            $table->unique(['student_id', 'exam_id'], 'student_exam_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
