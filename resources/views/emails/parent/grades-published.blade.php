<x-mail::message>
# 📊 Results Published

Dear **{{ $student->parent_name }}**,

Results for **{{ $student->full_name }}** have been published for **{{ $term->name }}**.

## Quick Summary

@php
    $grades = \App\Models\Grade::where('student_id', $student->id)
        ->where('term_id', $term->id)
        ->get();
    $total = $grades->sum('total_score');
    $average = $grades->count() > 0 ? $total / $grades->count() : 0;
    $passed = $grades->whereIn('grade', ['A', 'B', 'C', 'D', 'E'])->count();
    $failed = $grades->where('grade', 'F')->count();
@endphp

| Subject | Grade | Remark |
|---------|-------|--------|
@foreach($grades as $grade)
| {{ $grade->subject->name }} | **{{ $grade->grade }}** | {{ $grade->remark }} |
@endforeach

### Overall Performance

| Metric | Result |
|--------|--------|
| **Average Score** | {{ number_format($average, 2) }}% |
| **Subjects Passed** | {{ $passed }} |
| **Subjects Failed** | {{ $failed }} |

<x-mail::button :url="$gradesUrl" color="primary">
View Full Results
</x-mail::button>

---

*Please review the results and contact the school if you have any questions.*

Need help? Contact us at [{{ $supportEmail }}](mailto:{{ $supportEmail }})

Thanks,<br>
The {{ $schoolName }} Academic Office
</x-mail::message>