<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Models\MedicalCondition;
use App\Models\Analgesic;
use App\Models\InductionAgent;
use App\Models\MaintenanceAgent;
use App\Models\MonitoringType;
use App\Models\Relaxant;
use App\Models\RegionalType;
use App\Models\IntubationMethod;
use App\Models\AdverseEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuditFormSettingController extends Controller
{
    /**
     * Audit Form Settings Dashboard Hub.
     */
    public function index()
    {
        $totalDoctors = Staff::count();
        $activeDoctors = Staff::where('is_active', true)->count();
        $hiddenDoctors = $totalDoctors - $activeDoctors;

        $masterDataCounts = [
            'conditions' => MedicalCondition::count(),
            'analgesics' => Analgesic::count(),
            'induction_agents' => InductionAgent::count(),
            'maintenance_agents' => MaintenanceAgent::count(),
            'monitoring_types' => MonitoringType::count(),
            'relaxants' => Relaxant::count(),
            'regional_types' => RegionalType::count(),
            'intubation_methods' => IntubationMethod::count(),
            'adverse_events' => AdverseEvent::count(),
        ];

        return view('admin.audit_form_settings.index', compact(
            'totalDoctors',
            'activeDoctors',
            'hiddenDoctors',
            'masterDataCounts'
        ));
    }

    /**
     * Manage which doctors appear in the audit form.
     */
    public function doctors(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', '');

        $query = Staff::withCount('surgeries');

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $doctors = $query->orderBy('name')->paginate(15)->withQueryString();

        $totalDoctors = Staff::count();
        $activeDoctors = Staff::where('is_active', true)->count();

        return view('admin.audit_form_settings.doctors', compact(
            'doctors',
            'search',
            'status',
            'totalDoctors',
            'activeDoctors'
        ));
    }

    /**
     * Add a new doctor or surgical staff to the audit roster.
     */
    public function storeDoctor(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:staff,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $doctor = Staff::create([
            'name' => trim($validated['name']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $statusMsg = $doctor->is_active ? 'is now appearing in the audit form' : 'is currently hidden from the audit form';

        return redirect()->route('admin.audit-form-settings.doctors')
            ->with('status', "Doctor '{$doctor->name}' has been added and {$statusMsg}.");
    }

    /**
     * Toggle a doctor's visibility in the audit form.
     */
    public function toggleDoctor(Staff $staff)
    {
        $staff->is_active = !$staff->is_active;
        $staff->save();

        $stateMsg = $staff->is_active ? 'now appearing in the audit form' : 'hidden from the audit form';

        return redirect()->back()
            ->with('status', "Doctor '{$staff->name}' is {$stateMsg}.");
    }

    /**
     * Update doctor's name.
     */
    public function updateDoctor(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('staff', 'name')->ignore($staff->staff_id, 'staff_id')],
            'is_active' => ['required', 'boolean'],
        ]);

        $staff->update([
            'name' => trim($validated['name']),
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()->route('admin.audit-form-settings.doctors')
            ->with('status', "Doctor '{$staff->name}' updated successfully.");
    }

    /**
     * Delete doctor/staff (only permitted if no historical audits are linked).
     */
    public function destroyDoctor(Staff $staff)
    {
        $surgeriesCount = $staff->surgeries()->count();
        if ($surgeriesCount > 0) {
            return redirect()->back()->with('error', "Cannot delete '{$staff->name}' because they are linked to {$surgeriesCount} historical clinical audit record(s). Instead, you can toggle their status to 'Hidden' so they do not appear in new audit forms.");
        }

        $name = $staff->name;

        // If a corresponding doctor user account exists without any surgeries, delete it as well to keep data consistent
        $doctorUser = User::where('name', $name)->where('role', 'doctor')->first();
        if ($doctorUser) {
            $doctorUser->delete();
        }

        $staff->delete();

        return redirect()->route('admin.audit-form-settings.doctors')
            ->with('status', "Doctor '{$name}' has been successfully deleted from the roster.");
    }
}
