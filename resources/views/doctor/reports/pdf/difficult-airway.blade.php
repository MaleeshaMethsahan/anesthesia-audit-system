<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Difficult Airway Registry</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9pt; color: #1e293b; background: #fff; }
        .header { border-bottom: 3px solid #d97706; padding-bottom: 12px; margin-bottom: 14px; }
        .h1 { font-size: 15pt; font-weight: bold; color: #d97706; }
        .institution { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .report-title { font-size: 11pt; font-weight: bold; color: #1e293b; margin-top: 6px; }
        .meta { font-size: 8pt; color: #94a3b8; }
        .filters { background: #fffbeb; border: 1px solid #fde68a; border-radius: 5px; padding: 6px 10px; margin-bottom: 12px; font-size: 8pt; color: #78350f; }
        table.data { width: 100%; border-collapse: collapse; font-size: 8pt; }
        table.data th { background: #fffbeb; text-align: left; padding: 5px 7px; font-size: 7.5pt; font-weight: bold; text-transform: uppercase; color: #92400e; border-bottom: 2px solid #fcd34d; }
        table.data td { padding: 5px 7px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: top; }
        table.data tr:nth-child(even) td { background: #fffbeb; }
        .badge { display: inline-block; padding: 1px 7px; border-radius: 10px; font-size: 7.5pt; font-weight: bold; }
        .badge-red { background: #fee2e2; color: #b91c1c; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-indigo { background: #e0e7ff; color: #4338ca; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .pill { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 7.5pt; background: #f1f5f9; color: #475569; margin: 1px; border: 1px solid #e2e8f0; }
        .footer { border-top: 1px solid #e2e8f0; padding-top: 6px; margin-top: 14px; font-size: 7pt; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <div style="display:table;width:100%;">
            <div style="display:table-cell;vertical-align:top;">
                <div class="h1">Anaesthesia Audit System</div>
                @if($meta)<div class="institution">{{ $meta->institution ?? '' }}</div>@endif
                <div class="report-title">Difficult Airway Registry</div>
            </div>
            <div style="display:table-cell;vertical-align:top;text-align:right;">
                <div class="meta">Generated: {{ now()->format('d M Y H:i') }}</div>
                <div class="meta">Total Cases: {{ $surgeries->count() }}</div>
            </div>
        </div>
    </div>

    @if(!empty(array_filter($filters)))
    <div class="filters">
        <strong>Filters Applied:</strong>
        @if(!empty($filters['date_from'])) From: {{ $filters['date_from'] }} @endif
        @if(!empty($filters['date_to'])) To: {{ $filters['date_to'] }} @endif
        @if(!empty($filters['specialty'])) Specialty: {{ $filters['specialty'] }} @endif
    </div>
    @endif

    <table class="data">
        <thead>
            <tr>
                <th>Date</th>
                <th>Patient / PHN</th>
                <th>Operation</th>
                <th>Specialty</th>
                <th>ASA</th>
                <th>Type</th>
                <th>Intubation Methods</th>
                <th>Induction Agents</th>
                <th>Relaxants</th>
            </tr>
        </thead>
        <tbody>
            @forelse($surgeries as $surgery)
            <tr>
                <td>{{ date('d M Y', strtotime($surgery->date_of_surgery)) }}</td>
                <td>
                    <strong>{{ $surgery->patient->name ?? 'N/A' }}</strong><br>
                    <span style="color:#64748b;">{{ $surgery->patient->phn ?? '' }}</span>
                </td>
                <td>{{ $surgery->operation_name }}</td>
                <td>{{ $surgery->surgical_specialty ?? 'N/A' }}</td>
                <td><span class="badge badge-indigo">{{ $surgery->asa_grade ?? 'N/A' }}</span></td>
                <td><span class="badge {{ $surgery->surgery_type === 'Em' ? 'badge-red' : 'badge-blue' }}">{{ $surgery->surgery_type === 'Em' ? 'Em' : 'Elect' }}</span></td>
                <td>@foreach(optional($surgery->anaesthesia)->intubationMethods ?? [] as $m)<span class="pill">{{ $m->method_name }}</span>@endforeach</td>
                <td>@foreach(optional($surgery->anaesthesia)->inductionAgents ?? [] as $a)<span class="pill">{{ $a->name }}</span>@endforeach</td>
                <td>@foreach(optional($surgery->anaesthesia)->relaxants ?? [] as $r)<span class="pill">{{ $r->name }}</span>@endforeach</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;color:#94a3b8;padding:16px;">No cases found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Anaesthesia Audit System &bull; {{ $meta->institution ?? 'Department of Anaesthesia' }} &bull; Printed: {{ now()->format('d M Y H:i') }} &bull; CONFIDENTIAL</div>
</body>
</html>
