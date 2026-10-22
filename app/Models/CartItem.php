<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Persistent per-user cart, used by the API (Bearer-token clients have
 * no reliable session to keep a session-cart alive between requests).
 * The web site keeps its own session-based cart for guests — see
 * App\Http\Controllers\Frontend\CartController.
 */
class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
