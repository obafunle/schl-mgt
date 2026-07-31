<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Timetable - {{ $class->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
        }
        .timetable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .timetable th {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
        }
        .timetable td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: center;
            vertical-align: middle;
        }
        .timetable .period-cell {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .timetable .entry-cell {
            background-color: #f0f7ff;
        }
        .timetable .empty-cell {
            color: #ccc;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .legend {
            margin-top: 20px;
            font-size: 9px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .conflict {
            color: #cc0000;
            font-weight: bold;
        }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .mt-10 { margin-top: 10px; }
        .mb-5 { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p><strong>Class Timetable</strong></p>
        <p>
            {{ $class->name }} @if($classArm)({{ $classArm->name }})@endif | 
            {{ $term->name }} | 
            {{ $academicYear->name }}
        </p>
        <p>
            Generated: {{ now()->format('F d, Y h:i A') }}
        </p>
    </div>

    <table class="timetable">
        <thead>
            <tr>
                <th style="width: 15%;">Period / Time</th>
                @foreach($timetable['days'] as $day)
                    <th style="width: 17%;">{{ $day->short_name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($timetable['grid'] as $row)
                <tr>
                    <td class="period-cell">
                        {{ $row['period']->name }}
                        <br>
                        <span style="font-size: 8px; color: #666;">{{ $row['time'] }}</span>
                    </td>
                    @foreach($timetable['days'] as $day)
                        <td class="{{ isset($row['entries'][$day->id]) ? 'entry-cell' : '' }}">
                            @php
                                $entry = $row['entries'][$day->id] ?? null;
                            @endphp
                            @if($entry)
                                <div>
                                    <div class="font-bold">{{ $entry['subject'] }}</div>
                                    <div style="font-size: 8px;">{{ $entry['teacher'] }}</div>
                                    <div style="font-size: 8px; color: #666;">Room: {{ $entry['room'] }}</div>
                                    @if(isset($entry['has_conflict']) && $entry['has_conflict'])
                                        <div class="conflict" style="font-size: 8px;">⚠️ Conflict</div>
                                    @endif
                                </div>
                            @else
                                <span class="empty-cell">—</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <p><strong>Legend:</strong></p>
        <p>• All entries are scheduled for the current term</p>
        <p>• <span class="conflict">⚠️ Conflict</span> indicates a scheduling conflict (teacher, room, or class)</p>
        <p>• Total Entries: {{ $timetable['entries']->count() }}</p>
    </div>

    <div class="footer">
        <p>This is a system-generated timetable. For any changes, please contact the school administration.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>