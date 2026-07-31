<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'type', 'capacity', 'floor', 'building',
        'facilities', 'is_active', 'meta', 'created_by'
    ];

    protected $casts = [
        'facilities' => 'array',
        'meta' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function entries()
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function getTypeLabel()
    {
        return [
            'classroom' => 'Classroom',
            'laboratory' => 'Laboratory',
            'library' => 'Library',
            'auditorium' => 'Auditorium',
            'office' => 'Office',
            'other' => 'Other'
        ][$this->type] ?? $this->type;
    }

    public function getFacilitiesList()
    {
        return $this->facilities ? implode(', ', $this->facilities) : 'None';
    }

    public function isAvailable($dayId, $periodId)
    {
        return !$this->entries()
            ->where('day_id', $dayId)
            ->where('period_id', $periodId)
            ->where('status', 'scheduled')
            ->exists();
    }
}