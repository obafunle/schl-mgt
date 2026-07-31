<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffPerformanceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'reviewer_id', 'review_date', 'review_period',
        'punctuality', 'productivity', 'teamwork', 'communication',
        'technical_skills', 'leadership', 'problem_solving',
        'overall_rating', 'strengths', 'areas_for_improvement',
        'goals', 'reviewer_comments', 'staff_comments',
        'status', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'review_date' => 'date',
        'approved_at' => 'datetime',
        'punctuality' => 'integer',
        'productivity' => 'integer',
        'teamwork' => 'integer',
        'communication' => 'integer',
        'technical_skills' => 'integer',
        'leadership' => 'integer',
        'problem_solving' => 'integer',
        'overall_rating' => 'float'
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('review_period', $period);
    }

    // Methods
    public function getStatusLabel()
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'reviewed' => 'Reviewed',
            'approved' => 'Approved'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        return [
            'draft' => 'gray',
            'submitted' => 'blue',
            'reviewed' => 'yellow',
            'approved' => 'green'
        ][$this->status] ?? 'gray';
    }

    public function calculateOverallRating()
    {
        $ratings = [
            $this->punctuality,
            $this->productivity,
            $this->teamwork,
            $this->communication,
            $this->technical_skills,
            $this->leadership,
            $this->problem_solving
        ];
        
        $validRatings = array_filter($ratings);
        if (empty($validRatings)) {
            return null;
        }
        
        $this->overall_rating = round(array_sum($validRatings) / count($validRatings), 2);
        return $this->overall_rating;
    }

    public function getRatingLabel()
    {
        $rating = $this->overall_rating ?? $this->calculateOverallRating();
        
        if ($rating >= 4.5) return 'Excellent';
        if ($rating >= 3.5) return 'Good';
        if ($rating >= 2.5) return 'Satisfactory';
        if ($rating >= 1.5) return 'Needs Improvement';
        return 'Unsatisfactory';
    }

    public function getRatingColor()
    {
        $rating = $this->overall_rating ?? $this->calculateOverallRating();
        
        if ($rating >= 4.5) return 'green';
        if ($rating >= 3.5) return 'blue';
        if ($rating >= 2.5) return 'yellow';
        if ($rating >= 1.5) return 'orange';
        return 'red';
    }
}