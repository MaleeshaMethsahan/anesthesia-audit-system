<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adverse Events / M&amp;M Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9pt; color: #1e293b; background: #fff; }
        .header { border-bottom: 3px solid #dc2626; padding-bottom: 12px; margin-bottom: 14px; }
        .h1 { font-size: 15pt; font-weight: bold; color: #dc2626; }
        .institution { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .report-title { font-size: 11pt; font-weight: bold; color: #1e293b; margin-top: 6px; }
        .meta { font-size: 8pt; color: #94a3b8; }
        .filters { background: #fef2f2; border: 1px solid #fecaca; border-radius: 5px; padding: 6px 10px; margin-bottom: 10px; font-size: 8pt; color: #991b1b; }
        .summary { background: #fef2f2; border: 1px solid #fecaca; border-radius: 5px; padding: 8px 12px; margin-bottom: 12px; font-size: 9pt; }
        .event-section { margin-bottom: 14px; page-break-inside: avoid; }
        .event-header { background: #fef2f2; border-left: 4px solid #dc2626; padding: 5px 10px; margin-bottom: 5px; font-weight: bold; font-size: 9pt; color: #991b1b; }
        table.data { width: 100%; border-collapse: collapse; font-size: 8pt; }
        table.data th { background: #f8fafc; text-align: left; padding: 4px 7px; font-size: 7.5pt; font-weight: bold; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        table.data td { padding: 4px 7px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: top; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 7.5pt; font-weight: bold; }
        .badge-red { background: #fee2e2; color: #b91c1c; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-indigo { background: #e0e7ff; color: #4338ca; }
        .badge-danger { background: #dc2626; color: #fff; }
        .footer { border-top: 1px solid #e2e8f0; padding-top: 6px; margin-top: 14px; font-size: 7pt; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <div style="display:table;width:100%;">
            <div style="display:table-cell;vertical-align:top;">
                <div class="h1">Anaesthesia Audit System</div>
                @if($meta)<div class="institution">{{ $meta->institution ?? '' }}</div>@endif
                <div class="report-title">Adverse Events / Morbidity &amp; Mortality Report</div>
            </div>
            <div style="display:table-cell;vertical-align:top;text-align:right;">
                <div class="meta">Generated: {{ now()->format('d M Y H:i') }}</div>
            </div>
        </div>
    </div>

    @if(!empty(array_filter($filters)))
    <div class="filters">
        <strong>Filters:</strong>
        @if(!empty($filters['date_from'])) From: {{ $filters['date_from'] }} @endif
        @if(!empty($filters['date_to'])) To: {{ $filters['date_to'] }} @endif
        @if(!empty($filters['specialty'])) Specialty: {{ $filters['specialty'] }} @endif
        @if(!empty($filters['asa_grade'])) ASA: {{ $filters['asa_grade'] }} @endif
    </div>
    @endif

    <div class="summary">
        <strong>Summary:</strong> {{ $surgeries->count() }} case(s) with adverse events &bull;
        {{ count($groupedByEvent) }} distinct event type(s) &bull;
        {{ $surgeries->sum(fn($s) => $s->adverseEvents->count()) }} total event occurrence(s)
    </div>

    @forelse($groupedByEvent as $eventName => $cases)
    <div class="event-section">
        <div class="event-header">{{ $eventName }} — {{ count($cases) }} case(s)</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient / PHN</th>
                    <th>Operation</th>
                    <th>ASA</th>
                    <th>Type</th>
                    <th>Anaesthesia</th>
                    <th>All Events</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cases as $surgery)
                <tr>
                    <td>{{ date('d M Y', strtotime($surgery->date_of_surgery)) }}</td>
                    <td><strong>{{ $surgery->patient->name ?? 'N/A' }}</strong><br><span style="color:#64748b;">{{ $surgery->patient->phn ?? '' }}</span></td>
                    <td>{{ $surgery->operation_name }}</td>
                    <td><span class="badge badge-indigo">{{ $surgery->asa_grade ?? 'N/A' }}</span></td>
                    <td><span class="badge {{ $surgery->surgery_type === 'Em' ? 'badge-red' : 'badge-blue' }}">{{ $surgery->surgery_type === 'Em' ? 'Em' : 'Elect' }}</span></td>
                    <td>{{ optional($surgery->anaesthesia)->mode ?? 'N/A' }}</td>
                    <td>
                        @foreach($surgery->adverseEvents as $ae)
                            <span class="badge {{ $ae->event_name === $eventName ? 'badge-danger' : 'badge-red' }}" style="margin-right:2px;">{{ $ae->event_name }}</span>
                        @endforeach
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @empty
    <p style="color:#94a3b8;text-align:center;padding:16px;">No adverse event cases found.</p>
    @endforelse

    <div class="footer">Anaesthesia Audit System &bull; {{ $meta->institution ?? 'Department of Anaesthesia' }} &bull; Printed: {{ now()->format('d M Y H:i') }} &bull; CONFIDENTIAL — FOR CLINICAL USE ONLY</div>
</body>
</html>
