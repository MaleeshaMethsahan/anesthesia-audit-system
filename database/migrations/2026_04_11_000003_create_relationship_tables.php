<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_conditions', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('condition_id');
            $table->timestamp('recorded_at')->useCurrent();
            $table->index('patient_id', 'idx_patient_conditions_patient');
            $table->unique(['patient_id', 'condition_id'], 'uq_patient_condition');
            $table->foreign('patient_id', 'fk_patient_conditions_patient')->references('patient_id')->on('patients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('condition_id', 'fk_patient_conditions_condition')->references('condition_id')->on('medical_conditions')->cascadeOnUpdate();
        });

        Schema::create('surgery_adverse_events', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('surgery_id');
            $table->unsignedInteger('event_id');
            $table->unique(['surgery_id', 'event_id'], 'uq_sae');
            $table->foreign('surgery_id', 'fk_sae_surgery')->references('surgery_id')->on('surgeries')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('event_id', 'fk_sae_event')->references('event_id')->on('adverse_events')->cascadeOnUpdate();
        });

        Schema::create('surgery_analgesics', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('surgery_id');
            $table->unsignedInteger('analgesic_id');
            $table->unique(['surgery_id', 'analgesic_id'], 'uq_sa');
            $table->foreign('surgery_id', 'fk_sa_surgery')->references('surgery_id')->on('surgeries')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('analgesic_id', 'fk_sa_analgesic')->references('analgesic_id')->on('analgesics')->cascadeOnUpdate();
        });

        Schema::create('surgery_monitoring', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('surgery_id');
            $table->unsignedInteger('monitoring_id');
            $table->unique(['surgery_id', 'monitoring_id'], 'uq_surgery_monitoring');
            $table->foreign('surgery_id', 'fk_sm_surgery')->references('surgery_id')->on('surgeries')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('monitoring_id', 'fk_sm_monitoring')->references('monitoring_id')->on('monitoring_types')->cascadeOnUpdate();
        });

        Schema::create('surgery_staff', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('surgery_id');
            $table->unsignedInteger('staff_id');
            $table->enum('role_in_case', ['Consultant Surgeon', 'Consultant Anaesthetist', 'MOA']);
            $table->unique(['surgery_id', 'staff_id', 'role_in_case'], 'uq_surgery_staff');
            $table->foreign('surgery_id', 'fk_ss_surgery')->references('surgery_id')->on('surgeries')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('staff_id', 'fk_ss_staff')->references('staff_id')->on('staff')->cascadeOnUpdate();
        });

        Schema::create('anaesthesia_induction_agents', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('anaesthesia_id');
            $table->unsignedInteger('induction_agent_id');
            $table->unique(['anaesthesia_id', 'induction_agent_id'], 'uq_aia');
            $table->foreign('anaesthesia_id', 'fk_aia_anaesthesia')->references('anaesthesia_id')->on('anaesthesia')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('induction_agent_id', 'fk_aia_agent')->references('induction_agent_id')->on('induction_agents')->cascadeOnUpdate();
        });

        Schema::create('anaesthesia_maintenance_agents', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('anaesthesia_id');
            $table->unsignedInteger('maintenance_agent_id');
            $table->unique(['anaesthesia_id', 'maintenance_agent_id'], 'uq_ama');
            $table->foreign('anaesthesia_id', 'fk_ama_anaesthesia')->references('anaesthesia_id')->on('anaesthesia')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('maintenance_agent_id', 'fk_ama_agent')->references('maintenance_agent_id')->on('maintenance_agents')->cascadeOnUpdate();
        });

        Schema::create('anaesthesia_regional_types', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('anaesthesia_id');
            $table->unsignedInteger('regional_type_id');
            $table->unique(['anaesthesia_id', 'regional_type_id'], 'uq_art');
            $table->foreign('anaesthesia_id', 'fk_art_anaesthesia')->references('anaesthesia_id')->on('anaesthesia')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('regional_type_id', 'fk_art_regional')->references('regional_type_id')->on('regional_types')->cascadeOnUpdate();
        });

        Schema::create('anaesthesia_relaxants', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('anaesthesia_id');
            $table->unsignedInteger('relaxant_id');
            $table->unique(['anaesthesia_id', 'relaxant_id'], 'uq_ar');
            $table->foreign('anaesthesia_id', 'fk_ar_anaesthesia')->references('anaesthesia_id')->on('anaesthesia')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('relaxant_id', 'fk_ar_relaxant')->references('relaxant_id')->on('relaxants')->cascadeOnUpdate();
        });

        Schema::create('anaesthesia_intubation_methods', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('anaesthesia_id');
            $table->unsignedBigInteger('method_id');
            $table->timestamps();
            $table->foreign('anaesthesia_id', 'fk_aim_anaesthesia')->references('anaesthesia_id')->on('anaesthesia')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('method_id', 'fk_aim_method')->references('method_id')->on('intubation_methods')->cascadeOnUpdate();
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