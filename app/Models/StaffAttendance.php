<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $table = 'staff_attendance';

    protected $fillable = [
        'staff_id', 'date', 'clock_in', 'clock_out', 'hours_worked',
        'status', 'notes', 'marked_by'
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'hours_worked' => 'decimal:2'
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class);
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

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    // Methods
    public function getStatusLabel()
    {
        return [
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'half-day' => 'Half Day',
            'holiday' => 'Holiday',
            'leave' => 'Leave'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        return [
            'present' => 'green',
            'absent' => 'red',
            'late' => 'yellow',
            'half-day' => 'orange',
            'holiday' => 'blue',
            'leave' => 'purple'
        ][$this->status] ?? 'gray';
    }
}