<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'amount', 'frequency',
        'class_id', 'class_arm_id', 'is_mandatory', 'is_active',
        'meta', 'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'meta' => 'array'
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function classArm()
    {
        return $this->belongsTo(ClassArm::class);
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

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeForClass($query, $classId, $classArmId = null)
    {
        $query->where(function ($q) use ($classId, $classArmId) {
            $q->where('class_id', $classId)
              ->orWhereNull('class_id');
        });

        if ($classArmId) {
            $query->where(function ($q) use ($classArmId) {
                $q->where('class_arm_id', $classArmId)
                  ->orWhereNull('class_arm_id');
            });
        }

        return $query;
    }

    // Methods
    public function getFormattedAmount()
    {
        return '₦' . number_format($this->amount, 2);
    }

    public function getFrequencyLabel()
    {
        return [
            'one-time' => 'One Time',
            'termly' => 'Termly',
            'yearly' => 'Yearly',
            'monthly' => 'Monthly'
        ][$this->frequency] ?? $this->frequency;
    }
}