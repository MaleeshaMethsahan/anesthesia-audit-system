<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MedicalCondition;
use App\Models\MedicalSystem;
use App\Models\MonitoringType;
use App\Models\InductionAgent;
use App\Models\MaintenanceAgent;
use App\Models\Relaxant;
use App\Models\RegionalType;
use App\Models\IntubationMethod;
use App\Models\Analgesic;
use App\Models\AdverseEvent;
use Exception;

class ReferenceDataController extends Controller
{
    public function index()
    {
        $medicalSystems = MedicalSystem::orderBy('system_name')->get();
        $conditions = MedicalCondition::with('system')->orderBy('condition_name')->get();
        $monitoringTypes = MonitoringType::orderBy('monitoring_name')->get();
        $inductionAgents = InductionAgent::orderBy('name')->get();
        $maintenanceAgents = MaintenanceAgent::orderBy('name')->get();
        $relaxants = Relaxant::orderBy('name')->get();
        $regionalTypes = RegionalType::orderBy('name')->get();
        $intubationMethods = IntubationMethod::orderBy('method_name')->get();
        $analgesics = Analgesic::orderBy('name')->get();
        $adverseEvents = AdverseEvent::orderBy('event_name')->get();

        return view('admin.reference_data.index', compact(
            'medicalSystems',
            'conditions',
            'monitoringTypes',
            'inductionAgents',
            'maintenanceAgents',
            'relaxants',
            'regionalTypes',
            'intubationMethods',
            'analgesics',
            'adverseEvents'
        ));
    }

    public function store(Request $request, string $type)
    {
        try {
            switch ($type) {
                case 'condition':
                    $validated = $request->validate([
                        'system_id' => 'required|exists:medical_systems,system_id',
                        'condition_name' => 'required|string|max:150',
                        'patient_type' => 'required|in:Adult,Paediatric',
                    ]);
                    MedicalCondition::create($validated);
                    break;

                case 'monitoring':
                    $validated = $request->validate([
                        'monitoring_name' => 'required|string|max:150|unique:monitoring_types,monitoring_name',
                    ]);
                    MonitoringType::create($validated);
                    break;

                case 'induction':
                    $validated = $request->validate([
                        'name' => 'required|string|max:150|unique:induction_agents,name',
                    ]);
                    InductionAgent::create($validated);
                    break;

                case 'maintenance':
                    $validated = $request->validate([
                        'name' => 'required|string|max:150|unique:maintenance_agents,name',
                    ]);
                    MaintenanceAgent::create($validated);
                    break;

                case 'relaxant':
                    $validated = $request->validate([
                        'name' => 'required|string|max:150|unique:relaxants,name',
                    ]);
                    Relaxant::create($validated);
                    break;

                case 'regional':
                    $validated = $request->validate([
                        'name' => 'required|string|max:150|unique:regional_types,name',
                    ]);
                    RegionalType::create($validated);
                    break;

                case 'intubation':
                    $validated = $request->validate([
                        'method_name' => 'required|string|max:150|unique:intubation_methods,method_name',
                    ]);
                    IntubationMethod::create($validated);
                    break;

                case 'analgesic':
                    $validated = $request->validate([
                        'name' => 'required|string|max:150|unique:analgesics,name',
                    ]);
                    Analgesic::create($validated);
                    break;

                case 'event':
                    $validated = $request->validate([
                        'event_name' => 'required|string|max:150|unique:adverse_events,event_name',
                    ]);
                    AdverseEvent::create($validated);
                    break;

                default:
                    return redirect()->back()->with('error', 'Invalid category type.');
            }

            return redirect()->back()->with('success', 'Item created successfully!');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to create item: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $type, int $id)
    {
        try {
            switch ($type) {
                case 'condition':
                    $model = MedicalCondition::findOrFail($id);
                    $validated = $request->validate([
                        'system_id' => 'required|exists:medical_systems,system_id',
                        'condition_name' => 'required|string|max:150',
                        'patient_type' => 'required|in:Adult,Paediatric',
                    ]);
                    $model->update($validated);
                    break;

                case 'monitoring':
                    $model = MonitoringType::findOrFail($id);
                    $validated = $request->validate([
                        'monitoring_name' => 'required|string|max:150|unique:monitoring_types,monitoring_name,' . $id . ',monitoring_id',
                    ]);
                    $model->update($validated);
                    break;

                case 'induction':
                    $model = InductionAgent::findOrFail($id);
                    $validated = $request->validate([
                        'name' => 'required|string|max:150|unique:induction_agents,name,' . $id . ',induction_agent_id',
                    ]);
                    $model->update($validated);
                    break;

                case 'maintenance':
                    $model = MaintenanceAgent::findOrFail($id);
                    $validated = $request->validate([
                        'name' => 'required|string|max:150|unique:maintenance_agents,name,' . $id . ',maintenance_agent_id',
                    ]);
                    $model->update($validated);
                    break;

                case 'relaxant':
                    $model = Relaxant::findOrFail($id);
                    $validated = $request->validate([
                        'name' => 'required|string|max:150|unique:relaxants,name,' . $id . ',relaxant_id',
                    ]);
                    $model->update($validated);
                    break;

                case 'regional':
                    $model = RegionalType::findOrFail($id);
                    $validated = $request->validate([
                        'name' => 'required|string|max:150|unique:regional_types,name,' . $id . ',regional_type_id',
                    ]);
                    $model->update($validated);
                    break;

                case 'intubation':
                    $model = IntubationMethod::findOrFail($id);
                    $validated = $request->validate([
                        'method_name' => 'required|string|max:150|unique:intubation_methods,method_name,' . $id . ',method_id',
                    ]);
                    $model->update($validated);
                    break;

                case 'analgesic':
                    $model = Analgesic::findOrFail($id);
                    $validated = $request->validate([
                        'name' => 'required|string|max:150|unique:analgesics,name,' . $id . ',analgesic_id',
                    ]);
                    $model->update($validated);
                    break;

                case 'event':
                    $model = AdverseEvent::findOrFail($id);
                    $validated = $request->validate([
                        'event_name' => 'required|string|max:150|unique:adverse_events,event_name,' . $id . ',event_id',
                    ]);
                    $model->update($validated);
                    break;

                default:
                    return redirect()->back()->with('error', 'Invalid category type.');
            }

            return redirect()->back()->with('success', 'Item updated successfully!');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update item: ' . $e->getMessage());
        }
    }

    public function destroy(string $type, int $id)
    {
        try {
            switch ($type) {
                case 'condition':
                    MedicalCondition::findOrFail($id)->delete();
                    break;
                case 'monitoring':
                    MonitoringType::findOrFail($id)->delete();
                    break;
                case 'induction':
                    InductionAgent::findOrFail($id)->delete();
                    break;
                case 'maintenance':
                    MaintenanceAgent::findOrFail($id)->delete();
                    break;
                case 'relaxant':
                    Relaxant::findOrFail($id)->delete();
                    break;
                case 'regional':
                    RegionalType::findOrFail($id)->delete();
                    break;
                case 'intubation':
                    IntubationMethod::findOrFail($id)->delete();
                    break;
                case 'analgesic':
                    Analgesic::findOrFail($id)->delete();
                    break;
                case 'event':
                    AdverseEvent::findOrFail($id)->delete();
                    break;
                default:
                    return redirect()->back()->with('error', 'Invalid category type.');
            }

            return redirect()->back()->with('success', 'Item deleted successfully!');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Cannot delete item because it is associated with existing clinical records.');
        }
    }
}
