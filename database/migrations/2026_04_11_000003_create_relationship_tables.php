<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients', 'patient_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('condition_id')->constrained('medical_conditions', 'condition_id')->cascadeOnUpdate();
            $table->timestamp('recorded_at')->useCurrent();
            $table->unique(['patient_id', 'condition_id'], 'uq_patient_condition');
        });

        Schema::create('surgery_adverse_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_id')->constrained('surgeries', 'surgery_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('event_id')->constrained('adverse_events', 'event_id')->cascadeOnUpdate();
            $table->unique(['surgery_id', 'event_id'], 'uq_sae');
        });

        Schema::create('surgery_analgesics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_id')->constrained('surgeries', 'surgery_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('analgesic_id')->constrained('analgesics', 'analgesic_id')->cascadeOnUpdate();
            $table->unique(['surgery_id', 'analgesic_id'], 'uq_sa');
        });

        Schema::create('surgery_monitoring', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_id')->constrained('surgeries', 'surgery_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('monitoring_id')->constrained('monitoring_types', 'monitoring_id')->cascadeOnUpdate();
            $table->unique(['surgery_id', 'monitoring_id'], 'uq_surgery_monitoring');
        });

        Schema::create('surgery_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_id')->constrained('surgeries', 'surgery_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('staff_id')->constrained('staff', 'staff_id')->cascadeOnUpdate();
            $table->enum('role_in_case', ['Consultant Surgeon', 'Consultant Anaesthetist', 'MOA']);
            $table->unique(['surgery_id', 'staff_id', 'role_in_case'], 'uq_surgery_staff');
        });

        Schema::create('anaesthesia_induction_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anaesthesia_id')->constrained('anaesthesia', 'anaesthesia_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('induction_agent_id')->constrained('induction_agents', 'induction_agent_id')->cascadeOnUpdate();
            $table->unique(['anaesthesia_id', 'induction_agent_id'], 'uq_aia');
        });

        Schema::create('anaesthesia_maintenance_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anaesthesia_id')->constrained('anaesthesia', 'anaesthesia_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('maintenance_agent_id')->constrained('maintenance_agents', 'maintenance_agent_id')->cascadeOnUpdate();
            $table->unique(['anaesthesia_id', 'maintenance_agent_id'], 'uq_ama');
        });

        Schema::create('anaesthesia_regional_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anaesthesia_id')->constrained('anaesthesia', 'anaesthesia_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('regional_type_id')->constrained('regional_types', 'regional_type_id')->cascadeOnUpdate();
            $table->unique(['anaesthesia_id', 'regional_type_id'], 'uq_art');
        });

        Schema::create('anaesthesia_relaxants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anaesthesia_id')->constrained('anaesthesia', 'anaesthesia_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('relaxant_id')->constrained('relaxants', 'relaxant_id')->cascadeOnUpdate();
            $table->unique(['anaesthesia_id', 'relaxant_id'], 'uq_ar');
        });

        Schema::create('anaesthesia_intubation_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anaesthesia_id')->constrained('anaesthesia', 'anaesthesia_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('method_id')->constrained('intubation_methods', 'method_id')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anaesthesia_intubation_methods');
        Schema::dropIfExists('anaesthesia_relaxants');
        Schema::dropIfExists('anaesthesia_regional_types');
        Schema::dropIfExists('anaesthesia_maintenance_agents');
        Schema::dropIfExists('anaesthesia_induction_agents');
        Schema::dropIfExists('surgery_staff');
        Schema::dropIfExists('surgery_monitoring');
        Schema::dropIfExists('surgery_analgesics');
        Schema::dropIfExists('surgery_adverse_events');
        Schema::dropIfExists('patient_conditions');
    }
};