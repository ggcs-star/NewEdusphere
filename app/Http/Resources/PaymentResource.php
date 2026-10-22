<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course' => new CourseResource($this->whenLoaded('course')),
            'payment_method' => $this->payment_method,
            'amount' => (float) $this->amount,
            'status' => $this->status,
            'razorpay_order_id' => $this->razorpay_order_id,
            'razorpay_payment_id' => $this->razorpay_payment_id,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
