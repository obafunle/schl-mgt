<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .school-logo {
            max-width: 100px;
            max-height: 80px;
            margin-bottom: 10px;
        }
        .student-info {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 3px 10px;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .grades-table th {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .grades-table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
        }
        .grades-table .grade-a { color: #2d8b2d; font-weight: bold; }
        .grades-table .grade-b { color: #4a90d9; font-weight: bold; }
        .grades-table .grade-c { color: #f5a623; font-weight: bold; }
        .grades-table .grade-d { color: #f5a623; font-weight: bold; }
        .grades-table .grade-e { color: #e74c3c; font-weight: bold; }
        .grades-table .grade-f { color: #c0392b; font-weight: bold; }
        .summary {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .summary table {
            width: 100%;
        }
        .summary td {
            padding: 5px 10px;
        }
        .remarks {
            margin-top: 20px;
            padding: 15px;
            border-top: 2px solid #000;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
        }
        .signature {
            margin-top: 30px;
            display: flex;
            justify-content: space-around;
        }
        .signature div {
            text-align: center;
        }
        .signature .line {
            width: 150px;
            border-bottom: 1px solid #000;
            margin: 30px auto 5px;
        }
        @page {
            margin: 20px 30px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-10 { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        @if($school['logo'])
            <img src="{{ $school['logo'] }}" class="school-logo" alt="School Logo">
        @endif
        <h1>{{ $school['name'] }}</h1>
        <p>{{ $school['address'] }}</p>
        <p>Phone: {{ $school['phone'] }} | Email: {{ $school['email'] }}</p>
        <h2>REPORT CARD</h2>
        <p><strong>{{ $reportCard->term->name }} - {{ $reportCard->academicYear->name }}</strong></p>
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td width="25%"><strong>Student Name:</strong></td>
                <td width="25%">{{ $reportCard->student->full_name }}</td>
                <td width="25%"><strong>Admission No:</strong></td>
                <td width="25%">{{ $reportCard->student->admission_number }}</td>
            </tr>
            <tr>
                <td><strong>Class:</strong></td>
                <td>{{ $reportCard->class->name }} {{ $reportCard->classArm->name }}</td>
                <td><strong>Gender:</strong></td>
                <td>{{ ucfirst($reportCard->student->gender) }}</td>
            </tr>
            <tr>
                <td><strong>Term:</strong></td>
                <td>{{ $reportCard->term->name }}</td>
                <td><strong>Academic Year:</strong></td>
                <td>{{ $reportCard->academicYear->name }}</td>
            </tr>
        </table>
    </div>

    <table class="grades-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>CA ({{ $grades->first()->examination->ca_weight ?? 40 }}%)</th>
                <th>Exam ({{ $grades->first()->examination->exam_weight ?? 60 }}%)</th>
                <th>Total</th>
                <th>Grade</th>
                <th>Remark</th>
                <th>Position</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grades as $index => $grade)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $grade->subject->name }}</td>
                    <td>{{ $grade->ca_score ?? '-' }}</td>
                    <td>{{ $grade->exam_score ?? '-' }}</td>
                    <td>{{ number_format($grade->total_score, 2) }}</td>
                    <td class="grade-{{ strtolower($grade->grade) }}">{{ $grade->grade ?? '-' }}</td>
                    <td>{{ $grade->remark ?? '-' }}</td>
                    <td>{{ $grade->position ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td width="33%"><strong>Total Score:</strong> {{ number_format($reportCard->total_score, 2) }}</td>
                <td width="33%"><strong>Average:</strong> {{ number_format($reportCard->average_score, 2) }}</td>
                <td width="33%"><strong>GPA:</strong> {{ number_format($reportCard->grade_point_average, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Subjects Passed:</strong> {{ $reportCard->subjects_passed }}</td>
                <td><strong>Subjects Failed:</strong> {{ $reportCard->subjects_failed }}</td>
                <td><strong>Class Position:</strong> {{ $reportCard->position ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td colspan="3"><strong>Promotion Status:</strong> 