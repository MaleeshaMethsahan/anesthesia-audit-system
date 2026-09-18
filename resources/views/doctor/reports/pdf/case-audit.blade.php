<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Audit Report — Surgery #{{ $surgery->surgery_id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10pt; color: #1e293b; background: #fff; }
        .header { border-bottom: 3px solid #4f46e5; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { font-size: 16pt; font-weight: bold; color: #4f46e5; }
        .header .institution { font-size: 9pt; color: #64748b; margin-top: 2px; }
        .header .report-title { font-size: 12pt; font-weight: bold; color: #1e293b; margin-top: 6px; }
        .header .meta { font-size: 8pt; color: #94a3b8; margin-top: 2px; }
        .section { margin-bottom: 14px; }
        .section-title { font-size: 9pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #4f46e5; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 8px; }
        .grid-2 { display: table; width: 100%; }
        .grid-2 .col { display: table-cell; width: 50%; vertical-align: top; padding-right: 16px; }
        .grid-2 .col:last-child { padding-right: 0; }
        .dl { width: 100%; }
        .dl tr td { padding: 3px 0; font-size: 9pt; }
        .dl tr td:first-child { color: #64748b; width: 40%; }
        .dl tr td:last-child { font-weight: bold; color: #1e293b; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 8pt; font-weight: bold; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-red { background: #fee2e2; color: #b91c1c; }
        .badge-indigo { background: #e0e7ff; color: #4338ca; }
        .badge-amber { background: #fef3c7; color: #b45309; }
        .badge-ward { background: #2563eb; color: #fff; }
        .badge-hdu { background: #d97706; color: #fff; }
        .badge-icu { background: #dc2626; color: #fff; }
        .pills { margin-top: 4px; }
        .pill { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8pt; font-weight: 600; background: #f1f5f9; color: #475569; margin: 1px; border: 1px solid #e2e8f0; }
        .danger-box { background: #fef2f2; border: 1.5px solid #fca5a5; border-radius: 6px; padding: 8px 12px; margin-bottom: 10px; color: #b91c1c; font-weight: bold; font-size: 9pt; }
        table.data { width: 100%; border-collapse: collapse; font-size: 9pt; }
        table.data th { background: #f8fafc; text-align: left; padding: 6px 8px; font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        table.data td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .footer { border-top: 1px solid #e2e8f0; padding-top: 6px; margin-top: 16px; font-size: 7pt; color: #94a3b8; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="grid-2">
            <div class="col">
                <div class="h1">Anaesthesia Audit System</div>
                @if($meta)
                <div class="institution">{{ $meta->institution ?? '' }}</div>
                @endif
                <div class="report-title">Case Audit Report — Surgery #{{ $surgery->surgery_id }}</div>
            </div>
            <div class="col" style="text-align:right;">
                <div class="meta">Generated: {{ now()->format('d M Y H:i') }}</div>
                <div class="meta">Date of Surgery: {{ date('d M Y', strtotime($surgery->date_of_surgery)) }}</div>
            </div>
        </div>
    </div>

    @php
        $patient = $surgery->patient;
        $anaes   = $surgery->anaesthesia;
        $adm     = $surgery->post_op_admission;
        $admBadge = $adm === 'ICU' ? 'badge-icu' : ($adm === 'HDU' ? 'badge-hdu' : 'badge-ward');
    @endphp

    @if($anaes && $anaes->difficult_intubation)
    <div class="danger-box">⚠ DIFFICULT AIRWAY — Documented difficult intubation on record for this case.</div>
    @endif

    <!-- Patient & Surgery -->
    <div class="section">
        <div class="section-title">Patient &amp; Surgery Information</div>
        <div class="grid-2">
            <div class="col">
                <table class="dl">
                    <tr><td>Patient Name</td><td>{{ $patient->name ?? 'N/A' }}</td></tr>
                    <tr><td>PHN</td><td>{{ $patient->phn ?? 'N/A' }}</td></tr>
                    <tr><td>BHT</td><td>{{ $patient->bht ?? 'N/A' }}</td></tr>
                    <tr><td>Age</td><td>{{ $patient->age ?? 'N/A' }}</td></tr>
                    <tr><td>Sex</td><td>{{ $patient->sex ?? 'N/A' }}</td></tr>
                </table>
            </div>
            <div class="col">
                <table class="dl">
                    <tr><td>Operation</td><td>{{ $surgery->operation_name }}</td></tr>
                    <tr><td>Specialty</td><td>{{ $surgery->surgical_specialty ?? 'N/A' }}</td></tr>
                    <tr><td>Surgery Type</td><td><span class="badge {{ $surgery->surgery_type === 'Em' ? 'badge-red' : 'badge-blue' }}">{{ $surgery->surgery_type === 'Em' ? 'Emergency' : 'Elective' }}</span></td></tr>
                    <tr><td>ASA Grade</td><td><span class="badge badge-indigo">{{ $surgery->asa_grade ?? 'N/A' }}</span></td></tr>
                    <tr><td>Post-Op</td><td><span class="badge {{ $admBadge }}">{{ $adm ?? 'N/A' }}</span></td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Comorbidities -->
    @if($patient && $patient->conditions->isNotEmpty())
    <div class="section">
        <div class="section-title">Comorbidities</div>
        <div class="pills">
            @foreach($patient->conditions as $cond)
                <span class="pill">{{ $cond->condition_name }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Anaesthesia -->
    @if($anaes)
    <div class="section">
        <div class="section-title">Anaesthesia Details</div>
        <div class="grid-2">
            <div class="col">
                <table class="dl">
                    <tr><td>Mode</td><td>{{ $anaes->mode ?? 'N/A' }}</td></tr>
                    <tr><td>Induction Type</td><td>{{ $anaes->induction_type ?? 'N/A' }}</td></tr>
                    <tr><td>Maintenance Route</td><td>{{ $anaes->maintenance_route ?? 'N/A' }}</td></tr>
                    <tr><td>Difficult Intubation</td><td style="{{ $anaes->difficult_intubation ? 'color:#dc2626;font-weight:bold;' : '' }}">{{ $anaes->difficult_intubation ? 'YES ⚠' : 'No' }}</td></tr>
                    <tr><td>Relaxant Used</td><td>{{ $anaes->relaxant_used ? 'Yes' : 'No' }}</td></tr>
                </table>
            </div>
            <div class="col">
                @if($anaes->inductionAgents->isNotEmpty())
                    <p style="font-size:8pt;color:#64748b;font-weight:bold;margin-bottom:3px;">Induction Agents</p>
                    <div class="pills" style="margin-bottom:6px;">@foreach($anaes->inductionAgents as $a)<span class="pill">{{ $a->name }}</span>@endforeach</div>
                @endif
                @if($anaes->maintenanceAgents->isNotEmpty())
                    <p style="font-size:8pt;color:#64748b;font-weight:bold;margin-bottom:3px;">Maintenance Agents</p>
                    <div class="pills" style="margin-bottom:6px;">@foreach($anaes->maintenanceAgents as $a)<span class="pill">{{ $a->name }}</span>@endforeach</div>
                @endif
                @if($anaes->relaxants->isNotEmpty())
                    <p style="font-size:8pt;color:#64748b;font-weight:bold;margin-bottom:3px;">Relaxants</p>
                    <div class="pills" style="margin-bottom:6px;">@foreach($anaes->relaxants as $r)<span class="pill">{{ $r->name }}</span>@endforeach</div>
                @endif
                @if($anaes->intubationMethods->isNotEmpty())
                    <p style="font-size:8pt;color:#64748b;font-weight:bold;margin-bottom:3px;">Intubation Methods</p>
                    <div class="pills">@foreach($anaes->intubationMethods as $m)<span class="pill">{{ $m->method_name }}</span>@endforeach</div>
                @endif
            </div>
        </div>
        @if($anaes->notes)
        <p style="margin-top:8px;font-size:9pt;color:#475569;"><strong>Notes:</strong> {{ $anaes->notes }}</p>
        @endif
    </div>
    @endif

    <!-- Monitoring + Analgesics -->
    <div class="section">
        <div class="section-title">Monitoring &amp; Analgesia</div>
        <div class="grid-2">
            <div class="col">
                <p style="font-size:8pt;color:#64748b;font-weight:bold;margin-bottom:4px;">Monitoring Types</p>
                <div class="pills">
                    @forelse($surgery->monitoringTypes as $mt)<span class="pill">{{ $mt->monitoring_name }}</span>@empty<span style="font-size:9pt;color:#94a3b8;">None recorded</span>@endforelse
                </div>
            </div>
            <div class="col">
                <p style="font-size:8pt;color:#64748b;font-weight:bold;margin-bottom:4px;">Analgesics</p>
                <div class="pills">
                    @forelse($surgery->analgesics as $an)<span class="pill">{{ $an->name }}</span>@empty<span style="font-size:9pt;color:#94a3b8;">None recorded</span>@endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Adverse Events -->
    @if($surgery->adverseEvents->isNotEmpty())
    <div class="section">
        <div class="danger-box" style="font-weight:normal;">
            <strong>Adverse Events:</strong>
            @foreach($surgery->adverseEvents as $ae)
                <span class="badge badge-red" style="margin-left:4px;">{{ $ae->event_name }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Staff -->
    @if($surgery->staff->isNotEmpty())
    <div class="section">
        <div class="section-title">Clinical Staff</div>
        <table class="data">
            <thead><tr><th>Name</th><th>Role</th></tr></thead>
            <tbody>
                @foreach($surgery->staff as $s)
                <tr><td>{{ $s->name }}</td><td>{{ $s->pivot->role_in_case }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Anaesthesia Audit System &bull; {{ $meta->institution ?? 'Department of Anaesthesia' }} &bull; Printed: {{ now()->format('d M Y H:i') }} &bull; CONFIDENTIAL
    </div>
</body>
</html>
