<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->text('name')->change();
        });

        // Encrypt any existing plain-text patient names in the database
        $patients = DB::table('patients')->get();
        foreach ($patients as $patient) {
            if (!empty($patient->name)) {
                try {
                    Crypt::decryptString($patient->name);
                } catch (\Throwable $e) {
                    // Not encrypted yet, encrypt it now
                    DB::table('patients')
                        ->where('patient_id', $patient->patient_id)
                        ->update(['name' => Crypt::encryptString($patient->name)]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $patients = DB::table('patients')->get();
        foreach ($patients as $patient) {
            if (!empty($patient->name)) {
                try {
                    $decrypted = Crypt::decryptString($patient->name);
                    DB::table('patients')
                        ->where('patient_id', $patient->patient_id)
                        ->update(['name' => $decrypted]);
                } catch (\Throwable $e) {
                    // Already plain text
                }
            }
        }

        Schema::table('patients', function (Blueprint $table) {
            $table->string('name', 150)->change();
        });
    }
};
