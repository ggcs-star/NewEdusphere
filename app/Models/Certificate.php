<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_template_id',
        'certificate_number',
        'data',
        'issued_at',
    ];

    protected $casts = [
        'data' => 'array',
        'issued_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(
            CertificateTemplate::class,
            'certificate_template_id'
        );
    }
}
