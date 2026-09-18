<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Case Log — Dr. {{ $doctorName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9pt; color: #1e293b; background: #fff; }
        .header { border-bottom: 3px solid #059669; padding-bottom: 12px; margin-bottom: 14px; }
        .h1 { font-size: 15pt; font-weight: bold; color: #059669; }
        .institution { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .report-title { font-size: 11pt; font-weight: bold; color: #1e293b; margin-top: 6px; }
        .meta { font-size: 8pt; color: #94a3b8; }
        .kpi-row { display: table; width: 100%; margin-bottom: 12px; border-spacing: 8px; }
        .kpi { display: table-cell; background: #f0fdf4; border: 1px solid #a7f3d0; border-radius: 5px; padding: 8px 14px; text-align: center; }
        .kpi-val { font-size: 18pt; font-weight: bold; color: #059669; }
        .kpi-label { font-size: 8pt; color: #64748b; font-weight: bold; text-transform: uppercase; }
        .filters { background: #f0fdf4; border: 1px solid #a7f3d0; border-radius: 5px; padding: 6px 10px; margin-bottom: 10px; font-size: 8pt; color: #065f46; }
        table.data { width: 100%; border-collapse: collapse; font-size: 8pt; }
        table.data th { background: #f0fdf4; text-align: left; padding: 5px 7px; font-size: 7.5pt; font-weight: bold; text-transform: uppercase; color: #065f46; border-bottom: 2px solid #6ee7b7; }
        table.data td { padding: 5px 7px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: top; }
        table.data tr:nth-child(even) td { background: #f0fdf4; }
        .badge { display: inline-block; padding: 1px 7px; border-radius: 10px; font-size: 7.5pt; font-weight: bold; }
        .badge-red { background: #fee2e2; color: #b91c1c; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-indigo { background: #e0e7ff; color: #4338ca; }
        .badge-ward { background: #2563eb; color: #fff; }
        .badge-hdu { background: #d97706; color: #fff; }
        .badge-icu { background: #dc2626; color: #fff; }
        .ae-flag { color: #b91c1c; font-size: 7.5pt; font-weight: bold; }
        .footer { border-top: 1px solid #e2e8f0; padding-top: 6px; margin-top: 14px; font-size: 7pt; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <div style="display:table;width:100%;">
            <div style="display:table-cell;vertical-align:top;">
                <div class="h1">Anaesthesia Audit System</div>
                @if($meta)<div class="institution">{{ $meta->institution ?? '' }}</div>@endif
                <div class="report-title">Case Log — Dr. {{ $doctorName }}</div>
            </div>
            <div style="display:table-cell;vertical-align:top;text-align:right;">
                <div class="meta">Generated: {{ now()->format('d M Y H:i') }}</div>
                @if(!empty($filters['date_from']) || !empty($filters['date_to']))
                <div class="meta">Period: {{ $filters['date_from'] ?: 'All time' }} — {{ $filters['date_to'] ?: 'Present' }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="kpi-row">
        <div class="kpi"><div class="kpi-val">{{ $totalCases }}</div><div class="kpi-label">Total Cases</div></div>
        <div class="kpi"><div class="kpi-val" style="color:#dc2626;">{{ $emCases }}</div><div class="kpi-label">Emergency</div></div>
        <div class="kpi"><div class="kpi-val" style="color:#2563eb;">{{ $electiveCases }}</div><div class="kpi-label">Elective</div></div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>Date</th>
                <th>Patient / PHN</th>
                <th>Operation</th>
                <th>Specialty</th>
                <th>Type</th>
                <th>ASA</th>
                <th>Anaesthesia</th>
                <th>Post-Op</th>
                <th>My Role</th>
                <th>AE</th>
            </tr>
        </thead>
        <tbody>
            @forelse($surgeries as $surgery)
            @php
                $myRole = $surgery->staff->firstWhere('name', $doctorName)?->pivot->role_in_case ?? 'Staff';
                $adm = $surgery->post_op_admission;
                $admBadge = $adm === 'ICU' ? 'badge-icu' : ($adm === 'HDU' ? 'badge-hdu' : 'badge-ward');
            @endphp
            <tr>
                <td>{{ date('d M Y', strtotime($surgery->date_of_surgery)) }}</td>
                <td><strong>{{ $surgery->patient->name ?? 'N/A' }}</strong><br><span style="color:#64748b;">{{ $surgery->patient->phn ?? '' }}</span></td>
                <td>{{ $surgery->operation_name }}</td>
                <td>{{ $surgery->surgical_specialty ?? 'N/A' }}</td>
                <td><span class="badge {{ $surgery->surgery_type === 'Em' ? 'badge-red' : 'badge-blue' }}">{{ $surgery->surgery_type === 'Em' ? 'Em' : 'Elect' }}</span></td>
                <td><span class="badge badge-indigo">{{ $surgery->asa_grade ?? 'N/A' }}</span></td>
                <td>{{ optional($surgery->anaesthesia)->mode ?? 'N/A' }}</td>
                <td><span class="badge {{ $admBadge }}">{{ $adm ?? 'N/A' }}</span></td>
                <td style="font-size:7.5pt;">{{ $myRole }}</td>
                <td>
                    @if($surgery->adverseEvents->isNotEmpty())
                        <span class="ae-flag">⚠ {{ $surgery->adverseEvents->count() }}</span>
                    @else
                        <span style="color:#94a3b8;">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;color:#94a3b8;padding:14px;">No cases found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Anaesthesia Audit System &bull; {{ $meta->institution ?? 'Department of Anaesthesia' }} &bull; Dr. {{ $doctorName }} &bull; Printed: {{ now()->format('d M Y H:i') }} &bull; CONFIDENTIAL</div>
</body>
</html>
