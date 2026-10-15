<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
protected $table = 'payments';
   protected $fillable = [
    
    'user_id',
    'course_id',
    'payment_method',
    'amount',
    'admin_revenue',
    'instructor_revenue',
    'instructor_paid_out',
    'razorpay_order_id',
    'razorpay_payment_id',
    'razorpay_signature',
    'status',
];
 
protected $casts = [
    'amount' => 'decimal:2',
    'admin_revenue' => 'decimal:2',
    'instructor_revenue' => 'decimal:2',
    'instructor_paid_out' => 'boolean',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
