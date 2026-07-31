<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number', 'student_id', 'class_id', 'class_arm_id',
        'term_id', 'academic_year_id',
        'issue_date', 'due_date', 'subtotal', 'discount',
        'total', 'amount_paid', 'balance',
        'items', 'status', 'payment_reference', 'paid_at',
        'meta', 'created_by'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'items' => 'array',
        'meta' => 'array',
        'paid_at' => 'datetime'
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

    public function classArm()
    {
        return $this->belongsTo(ClassArm::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'partial', 'overdue']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->whereDate('due_date', '<', now())
                  ->whereIn('status', ['sent', 'partial']);
            });
    }

    public function scopeByTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    // Methods
    public function getStatusLabel()
    {
        return [
            'draft' => 'Draft',
            'sent' => 'Sent',
            'paid' => 'Paid',
            'partial' => 'Partial',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        return [
            'draft' => 'gray',
            'sent' => 'blue',
            'paid' => 'green',
            'partial' => 'yellow',
            'overdue' => 'red',
            'cancelled' => 'darkred'
        ][$this->status] ?? 'gray';
    }

    public function getBalanceAttribute()
    {
        return $this->total - $this->amount_paid;
    }

    public function isFullyPaid()
    {
        return $this->balance <= 0;
    }

    public function getItemsSummary()
    {
        $items = $this->items ?? [];
        $summary = [];
        foreach ($items as $item) {
            $summary[] = $item['name'] . ' - ₦' . number_format($item['amount'], 2);
        }
        return implode(', ', $summary);
    }

    public function getTotalItems()
    {
        return count($this->items ?? []);
    }

    public function markAsPaid($paymentReference = null)
    {
        $this->status = 'paid';
        $this->amount_paid = $this->total;
        $this->payment_reference = $paymentReference;
        $this->paid_at = now();
        $this->save();
    }

    public function updatePayment($amount)
    {
        $this->amount_paid += $amount;
        if ($this->amount_paid >= $this->total) {
            $this->status = 'paid';
            $this->paid_at = now();
        } else {
            $this->status = 'partial';
        }
        $this->save();
    }
}