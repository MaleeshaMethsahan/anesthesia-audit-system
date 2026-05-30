<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('patient_id');
            $table->string('name', 150);
            $table->integer('age')->nullable();
            $table->enum('sex', ['M', 'F']);
            $table->string('bht', 60)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->string('phn', 20)->unique();
        });

        Schema::create('medical_conditions', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('condition_id');
            $table->unsignedInteger('system_id');
            $table->string('condition_name', 150);
            $table->enum('patient_type', ['Adult', 'Paediatric']);
            $table->unique(['system_id', 'condition_name', 'patient_type'], 'uq_med_condition');
            $table->foreign('system_id', 'fk_med_conditions_system')->references('system_id')->on('medical_systems')->cascadeOnUpdate();
        });

        Schema::create('surgeries', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('surgery_id');
            $table->unsignedInteger('patient_id');
            $table->string('operation_name', 200);
            $table->string('surgical_specialty', 50)->nullable();
            $table->enum('surgery_type', ['Em', 'Elect']);
            $table->date('date_of_surgery')->nullable();
            $table->enum('asa_grade', ['I', 'II', 'III', 'IV', 'V', 'VI', 'E'])->nullable();
            $table->enum('post_op_admission', ['Ward', 'HDU', 'ICU'])->nullable();
            $table->string('adverse_event_other_details', 255)->nullable();
            $table->string('analgesia_other_details', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('patient_id', 'fk_surgeries_patient')->references('patient_id')->on('patients')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('anaesthesia', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('anaesthesia_id');
            $table->unsignedInteger('surgery_id');
            $table->enum('mode', ['GA', 'Regional']);
            $table->enum('induction_type', ['Gas', 'IV'])->nullable();
            $table->string('induction_other_details', 255)->nullable();
            $table->enum('maintenance_route', ['IV', 'Gas'])->nullable();
            $table->boolean('difficult_intubation')->default(false);
            $table->boolean('relaxant_used')->default(false);
            $table->string('regional_other_details', 255)->nullable();
            $table->text('notes')->nullable();
            $table->unique('surgery_id', 'uq_anaesthesia_surgery');
            $table->foreign('surgery_id', 'fk_anaesthesia_surgery')->references('surgery_id')->on('surgeries')->cascadeOnDelete()->cascadeOnUpdate();
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