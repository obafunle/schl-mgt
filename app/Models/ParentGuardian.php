<?php

/**
 * Parent Model
 *
 * This model manages parent/guardian profiles in the system.
 * Parents are linked to students through a many-to-many relationship.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParentGuardian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',               // Link to the user account
        'first_name',            // Parent's first name
        'last_name',             // Parent's last name
        'email',                 // Parent's email address
        'phone',                 // Parent's phone number
        'address',               // Parent's residential address
        'occupation',            // Parent's job/profession
        'photo',                 // Profile photo path
        'children_ids',          // Legacy: array of child IDs (deprecated)
        'email_notifications',   // Whether to send email notifications
        'sms_notifications',     // Whether to send SMS notifications
        'preferred_language',    // Preferred language for communication
        'email_verified_at',     // When the email was verified
        'verification_code',     // Email verification code
        'status',                // active, inactive, suspended
        'meta',                  // Additional JSON data
        'created_by'             // Who created this record
    ];

    /**
     * The attributes that should be cast to specific types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'children_ids' => 'array',              // JSON field becomes array
        'email_notifications' => 'boolean',     // Cast to boolean
        'sms_notifications' => 'boolean',       // Cast to boolean
        'email_verified_at' => 'datetime',      // Cast to Carbon datetime
        'meta' => 'array',                      // JSON field becomes array
    ];

    /* ============================================================
       RELATIONSHIPS
       ============================================================ */

    /**
     * Get the user account associated with this parent.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all children (students) associated with this parent.
     * Uses a many-to-many relationship through the parent_children table.
     */
    public function children()
    {
        return $this->belongsToMany(Student::class, 'parent_children')
                    ->withPivot('relationship', 'is_primary_contact', 'can_receive_notifications')
                    ->withTimestamps();
    }

    /**
     * Get all exeat requests made by this parent.
     */
    public function exeatRequests()
    {
        return $this->hasMany(ExeatRequest::class);
    }

    /**
     * Get the user who created this parent record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ============================================================
       ACCESSORS & MUTATORS
       ============================================================ */

    /**
     * Get the parent's full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the parent's profile photo URL.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->photo)) {
            return asset('storage/' . $this->photo);
        }
        // Fallback: use UI Avatars service
        $name = urlencode($this->full_name ?? 'Parent');
        return "https://ui-avatars.com/api/?name={$name}&size=100&background=4F46E5&color=fff&bold=true";
    }

    /**
     * Get the human-readable status label.
     */
    public function getStatusLabelAttribute()
    {
        return [
            'active'    => 'Active',
            'inactive'  => 'Inactive',
            'suspended' => 'Suspended'
        ][$this->status] ?? $this->status;
    }

    /**
     * Get the status color for UI badges.
     */
    public function getStatusColorAttribute()
    {
        return [
            'active'    => 'green',
            'inactive'  => 'gray',
            'suspended' => 'red'
        ][$this->status] ?? 'gray';
    }

    /* ============================================================
       SCOPES (Query Filters)
       ============================================================ */

    /**
     * Scope a query to only include active parents.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include parents who receive notifications.
     */
    public function scopeReceivesNotifications($query)
    {
        return $query->where('email_notifications', true)
                     ->orWhere('sms_notifications', true);
    }

    /* ============================================================
       HELPER METHODS
       ============================================================ */

    /**
     * Get the number of children linked to this parent.
     */
    public function getChildrenCount()
    {
        return $this->children()->count();
    }

    /**
     * Get all active children (students with status 'active').
     */
    public function getActiveChildren()
    {
        return $this->children()->where('status', 'active')->get();
    }

    /**
     * Get all pending exeat requests made by this parent.
     */
    public function getPendingExeatRequests()
    {
        return $this->exeatRequests()->pending()->get();
    }

    /**
     * Check if the parent's email is verified.
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Send a notification to the parent (email or SMS).
     * This method is a placeholder for the notification service.
     */
    public function notify($message, $type = 'email')
    {
        // This will be implemented with the NotificationService
        // Placeholder for now
        \Illuminate\Support\Facades\Log::info("Notification sent to {$this->email}: {$message}");
    }

    /**
     * Link a student to this parent.
     * Creates a relationship in the parent_children pivot table.
     */
    public function linkChild($studentId, $relationship = 'guardian', $isPrimary = false)
    {
        return $this->children()->attach($studentId, [
            'relationship' => $relationship,
            'is_primary_contact' => $isPrimary,
            'can_receive_notifications' => true,
        ]);
    }

    /**
     * Unlink a student from this parent.
     * Removes the relationship from the parent_children pivot table.
     */
    public function unlinkChild($studentId)
    {
        return $this->children()->detach($studentId);
    }
}
