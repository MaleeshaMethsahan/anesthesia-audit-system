<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Anaesthesia History — {{ $patient->phn ?? $phn }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10pt; color: #1e293b; background: #fff; }
        .header { border-bottom: 3px solid #4f46e5; padding-bottom: 12px; margin-bottom: 16px; }
        .h1 { font-size: 16pt; font-weight: bold; color: #4f46e5; }
        .institution { font-size: 9pt; color: #64748b; margin-top: 2px; }
        .report-title { font-size: 12pt; font-weight: bold; color: #1e293b; margin-top: 6px; }
        .meta { font-size: 8pt; color: #94a3b8; }
        .danger-banner { background: #dc2626; color: #fff; padding: 8px 12px; border-radius: 6px; margin-bottom: 8px; font-weight: bold; font-size: 9pt; }
        .warn-banner { background: #f97316; color: #fff; padding: 8px 12px; border-radius: 6px; margin-bottom: 8px; font-weight: bold; font-size: 9pt; }
        .section { margin-bottom: 14px; }
        .section-title { font-size: 9pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #4f46e5; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 8px; }
        .patient-header { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 12px; }
        .patient-header h2 { font-size: 13pt; font-weight: bold; color: #1e293b; }
        .patient-meta { font-size: 9pt; color: #64748b; margin-top: 2px; }
        table.data { width: 100%; border-collapse: collapse; font-size: 9pt; margin-bottom: 10px; }
        table.data th { background: #f8fafc; text-align: left; padding: 5px 7px; font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        table.data td { padding: 5px 7px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .badge { display: inline-block; padding: 1px 7px; border-radius: 10px; font-size: 8pt; font-weight: bold; }
        .badge-red { background: #fee2e2; color: #b91c1c; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-indigo { background: #e0e7ff; color: #4338ca; }
        .badge-danger { background: #dc2626; color: #fff; }
        .pill { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8pt; font-weight: 600; background: #f1f5f9; color: #475569; margin: 1px; border: 1px solid #e2e8f0; }
        .surgery-card { border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 10px; page-break-inside: avoid; }
        .surgery-card.difficult { border-color: #fca5a5; }
        .surgery-card-header { background: #f8fafc; padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-weight: bold; font-size: 9pt; }
        .surgery-card-body { padding: 8px 10px; }
        .footer { border-top: 1px solid #e2e8f0; padding-top: 6px; margin-top: 16px; font-size: 7pt; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <div style="display:table;width:100%;">
            <div style="display:table-cell;vertical-align:top;">
                <div class="h1">Anaesthesia Audit System</div>
                @if($meta)<div class="institution">{{ $meta->institution ?? '' }}</div>@endif
                <div class="report-title">Patient Anaesthesia History</div>
            </div>
            <div style="display:table-cell;vertical-align:top;text-align:right;">
                <div class="meta">Generated: {{ now()->format('d M Y H:i') }}</div>
            </div>
        </div>
    </div>

    @if($hasDifficultAirway)
    <div class="danger-banner">⚠ DIFFICULT AIRWAY ALERT — This patient has documented difficult intubation history. Review carefully before anaesthesia.</div>
    @endif
    @if($hasAdverseEvents)
    <div class="warn-banner">⚠ ADVERSE EVENTS ON RECORD — Previous anaesthetic adverse events documented. See below.</div>
    @endif

    @if($patient)
    <div class="patient-header">
        <h2>{{ $patient->name }}</h2>
        <div class="patient-meta">PHN: {{ $patient->phn }} &bull; BHT: {{ $patient->bht ?? 'N/A' }} &bull; Age: {{ $patient->age ?? 'N/A' }} &bull; Sex: {{ $patient->sex ?? 'N/A' }}</div>
        @if($patient->conditions->isNotEmpty())
        <div style="margin-top:6px;">
            <span style="font-size:8pt;color:#64748b;font-weight:bold;">Comorbidities: </span>
            @foreach($patient->conditions as $cond)<span class="pill">{{ $cond->condition_name }}</span>@endforeach
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Anaesthesia History ({{ $surgeries->count() }} procedure(s), most recent first)</div>
        @forelse($surgeries as $surgery)
        <div class="surgery-card {{ optional($surgery->anaesthesia)->difficult_intubation ? 'difficult' : '' }}">
            <div class="surgery-card-header">
                {{ date('d M Y', strtotime($surgery->date_of_surgery)) }} &bull;
                {{ $surgery->operation_name }}
                <span class="badge {{ $surgery->surgery_type === 'Em' ? 'badge-red' : 'badge-blue' }}" style="margin-left:6px;">{{ $surgery->surgery_type === 'Em' ? 'Emergency' : 'Elective' }}</span>
                @if(optional($surgery->anaesthesia)->difficult_intubation)
                    <span class="badge badge-danger" style="margin-left:6px;">⚠ Difficult Airway</span>
                @endif
            </div>
            <div class="surgery-card-body">
                <table class="data">
                    <tr>
                        <th>Specialty</th><th>ASA</th><th>Anaesthesia Mode</th><th>Post-Op</th>
                    </tr>
                    <tr>
                        <td>{{ $surgery->surgical_specialty ?? 'N/A' }}</td>
                        <td><span class="badge badge-indigo">{{ $surgery->asa_grade ?? 'N/A' }}</span></td>
                        <td>{{ optional($surgery->anaesthesia)->mode ?? 'N/A' }}</td>
                        <td>{{ $surgery->post_op_admission ?? 'N/A' }}</td>
                    </tr>
                </table>
                @if($surgery->adverseEvents->isNotEmpty())
                <p style="font-size:8pt;color:#b91c1c;font-weight:bold;margin-bottom:3px;">Adverse Events:</p>
                @foreach($surgery->adverseEvents as $ae)<span class="badge badge-red" style="margin-right:4px;">{{ $ae->event_name }}</span>@endforeach
                @endif
            </div>
        </div>
        @empty
        <p style="color:#94a3b8;font-size:9pt;">No surgical records found for this patient.</p>
        @endforelse
    </div>
    @else
    <p style="color:#94a3b8;font-size:9pt;">Patient not found for PHN: {{ $phn }}</p>
    @endif

    <div class="footer">Anaesthesia Audit System &bull; {{ $meta->institution ?? 'Department of Anaesthesia' }} &bull; Printed: {{ now()->format('d M Y H:i') }} &bull; CONFIDENTIAL</div>
</body>
</html>
