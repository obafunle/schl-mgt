<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffPayroll extends Model
{
    use HasFactory;

    protected $table = 'staff_payroll';

    protected $fillable = [
        'staff_id', 'payroll_period', 'month', 'year',
        'basic_salary', 'allowances', 'overtime_pay', 'bonus', 'other_earnings',
        'tax', 'pension', 'loan_deduction', 'other_deductions', 'deductions_details',
        'gross_pay', 'total_deductions', 'net_pay',
        'payment_status', 'payment_date', 'transaction_reference',
        'notes', 'processed_by', 'created_by'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'tax' => 'decimal:2',
        'pension' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'payment_date' => 'date',
        'other_earnings' => 'array',
        'deductions_details' => 'array'
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeByPeriod($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    // Methods
    public function getPaymentStatusLabel()
    {
        return [
            'pending' => 'Pending',
            'processed' => 'Processed',
            'paid' => 'Paid'
        ][$this->payment_status] ?? $this->payment_status;
    }

    public function getPaymentStatusColor()
    {
        return [
            'pending' => 'yellow',
            'processed' => 'blue',
            'paid' => 'green'
        ][$this->payment_status] ?? 'gray';
    }

    public function calculateNetPay()
    {
        $this->gross_pay = $this->basic_salary + $this->allowances + $this->overtime_pay + $this->bonus;
        
        // Add other earnings
        if (!empty($this->other_earnings)) {
            foreach ($this->other_earnings as $earning) {
                $this->gross_pay += $earning['amount'];
            }
        }

        $this->total_deductions = $this->tax + $this->pension + $this->loan_deduction + $this->other_deductions;
        
        // Add deductions details
        if (!empty($this->deductions_details)) {
            foreach ($this->deductions_details as $deduction) {
                $this->total_deductions += $deduction['amount'];
            }
        }

        $this->net_pay = $this->gross_pay - $this->total_deductions;
        
        return $this;
    }
}