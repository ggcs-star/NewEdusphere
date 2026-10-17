<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'title',
        'background_image',
        'fields',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'fields' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(
            Course::class,
            'certificate_template_id'
        );
    }
}
