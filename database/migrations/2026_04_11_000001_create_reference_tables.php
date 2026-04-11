<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adverse_events', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('event_id');
            $table->string('event_name', 120)->unique();
        });

        Schema::create('analgesics', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('analgesic_id');
            $table->string('name', 80)->unique();
        });

        Schema::create('induction_agents', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('induction_agent_id');
            $table->string('name', 80)->unique();
        });

        Schema::create('maintenance_agents', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('maintenance_agent_id');
            $table->string('name', 80)->unique();
        });

        Schema::create('monitoring_types', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('monitoring_id');
            $table->string('monitoring_name', 80)->unique();
        });

        Schema::create('regional_types', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('regional_type_id');
            $table->string('name', 80)->unique();
        });

        Schema::create('relaxants', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('relaxant_id');
            $table->string('name', 80)->unique();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('staff_id');
            $table->string('name', 150)->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('audit_form_meta', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('meta_id');
            $table->string('form_name', 150);
            $table->string('institution', 150)->nullable();
            $table->string('version_note', 150)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('intubation_methods', function (Blueprint $table) {
            $table->id('method_id');
            $table->string('method_name');
            $table->timestamps();
        });

        Schema::create('medical_systems', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('system_id');
            $table->string('system_code', 20)->unique();
            $table->string('system_name', 120);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_systems');
        Schema::dropIfExists('intubation_methods');
        Schema::dropIfExists('audit_form_meta');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('relaxants');
        Schema::dropIfExists('regional_types');
        Schema::dropIfExists('monitoring_types');
        Schema::dropIfExists('maintenance_agents');
        Schema::dropIfExists('induction_agents');
        Schema::dropIfExists('analgesics');
        Schema::dropIfExists('adverse_events');
    }
};