<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetablePeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'start_time', 'end_time', 'duration_minutes',
        'type', 'order', 'is_active', 'meta', 'created_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'meta' => 'array'
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

    public function scopeAcademic($query)
    {
        return $query->where('type', 'academic');
    }

    // Methods
    public function getTypeLabel()
    {
        return [
            'academic' => 'Academic',
            'break' => 'Break',
            'assembly' => 'Assembly',
            'sports' => 'Sports',
            'other' => 'Other'
        ][$this->type] ?? $this->type;
    }

    public function getTimeRange()
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }
}