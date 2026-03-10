<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anesthesia_records', function (Blueprint $table) {
            $table->id();
            $table->string('ip_number');
            $table->string('patient_name');
            $table->unsignedTinyInteger('age');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('surgery_date');
            $table->string('diagnosis');
            $table->string('procedure');
            $table->enum('anesthesia_type', ['GA', 'Spinal', 'Epidural', 'Regional', 'Local']);
            $table->string('surgeon')->nullable();
            $table->string('anesthetist')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->text('adverse_events')->nullable();
            $table->enum('post_op_destination', ['Ward', 'ICU', 'HDU', 'Recovery', 'Home'])->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anesthesia_records');
    }
};
