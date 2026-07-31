<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'short_name', 'day_number', 'is_school_day', 'order'
    ];

    protected $casts = [
        'is_school_day' => 'boolean'
    ];

    // Relationships
    public function entries()
    {
        return $this->hasMany(TimetableEntry::class);
    }

    // Scopes
    public function scopeSchoolDays($query)
    {
        return $query->where('is_school_day', true);
    }
}