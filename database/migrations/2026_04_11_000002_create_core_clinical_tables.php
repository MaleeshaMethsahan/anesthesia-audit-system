<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id('patient_id');
            $table->string('name', 150);
            $table->integer('age')->nullable();
            $table->enum('sex', ['M', 'F']);
            $table->string('bht', 60)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->string('phn', 20)->unique();
        });

        Schema::create('medical_conditions', function (Blueprint $table) {
            $table->id('condition_id');
            $table->foreignId('system_id')->constrained('medical_systems', 'system_id')->cascadeOnUpdate();
            $table->string('condition_name', 150);
            $table->enum('patient_type', ['Adult', 'Paediatric']);
            $table->unique(['system_id', 'condition_name', 'patient_type'], 'uq_med_condition');
        });

        Schema::create('surgeries', function (Blueprint $table) {
            $table->id('surgery_id');
            $table->foreignId('patient_id')->constrained('patients', 'patient_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('operation_name', 200);
            $table->string('surgical_specialty', 50)->nullable();
            $table->enum('surgery_type', ['Em', 'Elect']);
            $table->date('date_of_surgery')->nullable();
            $table->enum('asa_grade', ['I', 'II', 'III', 'IV', 'V', 'VI', 'E'])->nullable();
            $table->enum('post_op_admission', ['Ward', 'HDU', 'ICU'])->nullable();
            $table->string('adverse_event_other_details', 255)->nullable();
            $table->string('analgesia_other_details', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('anaesthesia', function (Blueprint $table) {
            $table->id('anaesthesia_id');
            $table->foreignId('surgery_id')->constrained('surgeries', 'surgery_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('mode', ['GA', 'Regional']);
            $table->enum('induction_type', ['Gas', 'IV'])->nullable();
            $table->string('induction_other_details', 255)->nullable();
            $table->enum('maintenance_route', ['IV', 'Gas'])->nullable();
            $table->boolean('difficult_intubation')->default(false);
            $table->boolean('relaxant_used')->default(false);
            $table->string('regional_other_details', 255)->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anaesthesia');
        Schema::dropIfExists('surgeries');
        Schema::dropIfExists('medical_conditions');
        Schema::dropIfExists('patients');
    }
};