<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'date',
        'status',
        'clock_in',
        'clock_out',
        'notes',
        'marked_by'
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->where('date', today());
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    // Methods
    public function getStatusLabel()
    {
        return [
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'excused' => 'Excused',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        return [
            'present' => 'green',
            'absent' => 'red',
            'late' => 'yellow',
            'excused' => 'blue',
        ][$this->status] ?? 'gray';
    }
}
