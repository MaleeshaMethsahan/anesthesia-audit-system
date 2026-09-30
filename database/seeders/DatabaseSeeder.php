<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin User
        User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'admin',
            'username' => 'admin',
            'password' => 'admin@gmail', // Ensure this is hashed in your User model mutator, or use Hash::make() here if not
            'role' => 'system_admin',
            'email_verified_at' => now(),
        ]);

        // 2. Seed Adverse Events
        DB::table('adverse_events')->insertOrIgnore([
            ['event_name' => 'Anaphylaxis'],
            ['event_name' => 'Arrhythmia'],
            ['event_name' => 'Bronchospasm'],
            ['event_name' => 'Drug reaction'],
            ['event_name' => 'Haemorrhage'],
            ['event_name' => 'Hypertension'],
            ['event_name' => 'Hypotension'],
            ['event_name' => 'Hypoxia'],
            ['event_name' => 'Laryngospasm'],
            ['event_name' => 'Others'],
        ]);

        // 3. Seed Analgesics
        DB::table('analgesics')->insertOrIgnore([
            ['name' => 'Fentanyl'],
            ['name' => 'Ketamine'],
            ['name' => 'Morphine'],
            ['name' => 'NSAID'],
            ['name' => 'Others'],
            ['name' => 'Paracetamol'],
            ['name' => 'Pethidine'],
        ]);

        // 4. Seed Induction Agents
        DB::table('induction_agents')->insertOrIgnore([
            ['name' => 'Ketamine'],
            ['name' => 'Others'],
            ['name' => 'Propofol'],
        ]);

        // 5. Seed Intubation Methods
        DB::table('intubation_methods')->insertOrIgnore([
            ['method_name' => 'Bougie'],
            ['method_name' => 'VL'],
            ['method_name' => 'Bronchoscope'],
            ['method_name' => 'Tracheostomy'],
        ]);

        // 6. Seed Maintenance Agents
        DB::table('maintenance_agents')->insertOrIgnore([
            ['name' => 'Isoflurane'],
            ['name' => 'Others'],
            ['name' => 'Sevoflurane'],
        ]);

        // 7. Seed Monitoring Types
        DB::table('monitoring_types')->insertOrIgnore([
            ['monitoring_name' => 'BIS'],
            ['monitoring_name' => 'Cardiac output'],
            ['monitoring_name' => 'CVP'],
            ['monitoring_name' => 'ECG'],
            ['monitoring_name' => 'EtCO2'],
            ['monitoring_name' => 'IABP'],
            ['monitoring_name' => 'NIBP'],
            ['monitoring_name' => 'SpO2'],
            ['monitoring_name' => 'Temp'],
        ]);

        // 8. Seed Regional Types
        DB::table('regional_types')->insertOrIgnore([
            ['name' => 'Caudal'],
            ['name' => 'Epidural'],
            ['name' => 'Other'],
            ['name' => 'SAB'],
        ]);

        // 9. Seed Relaxants
        DB::table('relaxants')->insertOrIgnore([
            ['name' => 'Atracurium'],
            ['name' => 'Pancuronium'],
            ['name' => 'Suxamethonium'],
            ['name' => 'Vecuronium'],
        ]);

        // 10. Seed Medical Systems
        // Note: Explicitly defining system_id so the foreign keys in medical_conditions line up perfectly.
        DB::table('medical_systems')->insertOrIgnore([
            ['system_id' => 1, 'system_code' => 'RS', 'system_name' => 'Respiratory'],
            ['system_id' => 2, 'system_code' => 'CVS', 'system_name' => 'Cardiovascular'],
            ['system_id' => 3, 'system_code' => 'GI', 'system_name' => 'Gastrointestinal'],
            ['system_id' => 4, 'system_code' => 'CNS', 'system_name' => 'Central Nervous System'],
            ['system_id' => 5, 'system_code' => 'Renal', 'system_name' => 'Renal'],
            ['system_id' => 6, 'system_code' => 'Endo', 'system_name' => 'Endocrine'],
            ['system_id' => 7, 'system_code' => 'Haemat', 'system_name' => 'Haematological'],
            ['system_id' => 8, 'system_code' => 'Others', 'system_name' => 'Others'],
            ['system_id' => 9, 'system_code' => 'Airway', 'system_name' => 'Airway'],
        ]);

        // 11. Seed Medical Conditions
        DB::table('medical_conditions')->insertOrIgnore([
            // Respiratory (1)
            ['system_id' => 1, 'condition_name' => 'BA', 'patient_type' => 'Adult'],
            ['system_id' => 1, 'condition_name' => 'BA', 'patient_type' => 'Paediatric'],
            ['system_id' => 1, 'condition_name' => 'COPD', 'patient_type' => 'Adult'],
            ['system_id' => 1, 'condition_name' => 'OSA', 'patient_type' => 'Adult'],
            ['system_id' => 1, 'condition_name' => 'OSA', 'patient_type' => 'Paediatric'],
            ['system_id' => 1, 'condition_name' => 'RTI', 'patient_type' => 'Adult'],
            ['system_id' => 1, 'condition_name' => 'RTI', 'patient_type' => 'Paediatric'],
            ['system_id' => 1, 'condition_name' => 'Smoking', 'patient_type' => 'Adult'],

            // Cardiovascular (2)
            ['system_id' => 2, 'condition_name' => 'Arrhythmia', 'patient_type' => 'Adult'],
            ['system_id' => 2, 'condition_name' => 'Congenital Heart disease', 'patient_type' => 'Paediatric'],
            ['system_id' => 2, 'condition_name' => 'DL', 'patient_type' => 'Adult'],
            ['system_id' => 2, 'condition_name' => 'H.Failure', 'patient_type' => 'Adult'],
            ['system_id' => 2, 'condition_name' => 'HTN', 'patient_type' => 'Adult'],
            ['system_id' => 2, 'condition_name' => 'IHD', 'patient_type' => 'Adult'],

            // Gastrointestinal (3)
            ['system_id' => 3, 'condition_name' => 'GORD', 'patient_type' => 'Paediatric'],
            ['system_id' => 3, 'condition_name' => 'Liver', 'patient_type' => 'Adult'],
            ['system_id' => 3, 'condition_name' => 'Liver', 'patient_type' => 'Paediatric'],
            ['system_id' => 3, 'condition_name' => 'N/V', 'patient_type' => 'Paediatric'],
            ['system_id' => 3, 'condition_name' => 'Reflux', 'patient_type' => 'Adult'],
            ['system_id' => 3, 'condition_name' => 'Ulcer', 'patient_type' => 'Adult'],

            // CNS (4)
            ['system_id' => 4, 'condition_name' => 'CP', 'patient_type' => 'Paediatric'],
            ['system_id' => 4, 'condition_name' => 'CVA', 'patient_type' => 'Adult'],
            ['system_id' => 4, 'condition_name' => 'Epilepsy', 'patient_type' => 'Adult'],
            ['system_id' => 4, 'condition_name' => 'Epilepsy', 'patient_type' => 'Paediatric'],
            ['system_id' => 4, 'condition_name' => 'Hydrocephalus', 'patient_type' => 'Paediatric'],

            // Renal (5)
            ['system_id' => 5, 'condition_name' => 'AKI', 'patient_type' => 'Adult'],
            ['system_id' => 5, 'condition_name' => 'AKI', 'patient_type' => 'Paediatric'],
            ['system_id' => 5, 'condition_name' => 'CKD', 'patient_type' => 'Adult'],
            ['system_id' => 5, 'condition_name' => 'CKD', 'patient_type' => 'Paediatric'],
            ['system_id' => 5, 'condition_name' => 'Nephrotic', 'patient_type' => 'Paediatric'],

            // Endocrine (6)
            ['system_id' => 6, 'condition_name' => 'Adrenal', 'patient_type' => 'Adult'],
            ['system_id' => 6, 'condition_name' => 'Adrenal', 'patient_type' => 'Paediatric'],
            ['system_id' => 6, 'condition_name' => 'DM', 'patient_type' => 'Adult'],
            ['system_id' => 6, 'condition_name' => 'DM', 'patient_type' => 'Paediatric'],
            ['system_id' => 6, 'condition_name' => 'Thyroid', 'patient_type' => 'Adult'],
            ['system_id' => 6, 'condition_name' => 'Thyroid', 'patient_type' => 'Paediatric'],

            // Haematological (7)
            ['system_id' => 7, 'condition_name' => 'Anaemia', 'patient_type' => 'Adult'],
            ['system_id' => 7, 'condition_name' => 'Anaemia', 'patient_type' => 'Paediatric'],
            ['system_id' => 7, 'condition_name' => 'Coagulopathy', 'patient_type' => 'Adult'],
            ['system_id' => 7, 'condition_name' => 'Coagulopathy', 'patient_type' => 'Paediatric'],
            ['system_id' => 7, 'condition_name' => 'Thrombocytopenia', 'patient_type' => 'Adult'],
            ['system_id' => 7, 'condition_name' => 'Thrombocytopenia', 'patient_type' => 'Paediatric'],

            // Others (8)
            ['system_id' => 8, 'condition_name' => 'Behavioral concerns', 'patient_type' => 'Paediatric'],
            ['system_id' => 8, 'condition_name' => 'Drug abuse', 'patient_type' => 'Adult'],
            ['system_id' => 8, 'condition_name' => 'Malignancy', 'patient_type' => 'Adult'],
            ['system_id' => 8, 'condition_name' => 'Malignancy', 'patient_type' => 'Paediatric'],
            ['system_id' => 8, 'condition_name' => 'Obesity', 'patient_type' => 'Adult'],
            ['system_id' => 8, 'condition_name' => 'Obesity', 'patient_type' => 'Paediatric'],
            ['system_id' => 8, 'condition_name' => 'Pregnancy', 'patient_type' => 'Adult'],
            ['system_id' => 8, 'condition_name' => 'Preterm', 'patient_type' => 'Paediatric'],
            ['system_id' => 8, 'condition_name' => 'Psychiatric', 'patient_type' => 'Adult'],
            ['system_id' => 8, 'condition_name' => 'Sepsis', 'patient_type' => 'Adult'],
            ['system_id' => 8, 'condition_name' => 'Sepsis', 'patient_type' => 'Paediatric'],

            // Airway (9)
            ['system_id' => 9, 'condition_name' => 'Craniofacial anomalies', 'patient_type' => 'Paediatric'],
        ]);
    }
}