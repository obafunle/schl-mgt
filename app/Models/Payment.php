<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id', 'reference', 'invoice_id', 'student_id',
        'amount', 'fee_charged', 'payment_method', 'gateway',
        'paystack_reference', 'paystack_authorization_code',
        'paystack_response', 'status', 'payment_date',
        'verified_at', 'meta', 'processed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_charged' => 'decimal:2',
        'paystack_response' => 'array',
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
        'meta' => 'array'
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Methods
    public function getStatusLabel()
    {
        return [
            'pending' => 'Pending',
            'success' => 'Successful',
            'failed' => 'Failed',
            'reversed' => 'Reversed'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        return [
            'pending' => 'yellow',
            'success' => 'green',
            'failed' => 'red',
            'reversed' => 'gray'
        ][$this->status] ?? 'gray';
    }

    public function getFormattedAmount()
    {
        return '₦' . number_format($this->amount, 2);
    }

    public function isSuccessful()
    {
        return $this->status === 'success';
    }
}