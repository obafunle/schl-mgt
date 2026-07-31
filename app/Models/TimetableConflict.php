<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableConflict extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_id', 'conflicting_entry_id', 'conflict_type',
        'description', 'is_resolved', 'resolution_notes'
    ];

    protected $casts = [
        'is_resolved' => 'boolean'
    ];

    // Relationships
    public function entry()
    {
        return $this->belongsTo(TimetableEntry::class, 'entry_id');
    }

    public function conflictingEntry()
    {
        return $this->belongsTo(TimetableEntry::class, 'conflicting_entry_id');
    }

    // Scopes
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    // Methods
    public function getConflictTypeLabel()
    {
        return [
            'teacher' => 'Teacher Conflict',
            'room' => 'Room Conflict',
            'class' => 'Class Conflict',
            'time' => 'Time Conflict'
        ][$this->conflict_type] ?? $this->conflict_type;
    }
}