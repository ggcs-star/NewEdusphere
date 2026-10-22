<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'certificate_number' => $this->certificate_number,
            'issued_at' => $this->issued_at?->toIso8601String(),
            'data' => $this->data,
            'course' => new CourseResource($this->whenLoaded('course')),
            'template' => $this->whenLoaded('template', fn () => [
                'id' => $this->template->id,
                'title' => $this->template->title,
                'background_image_url' => $this->template->background_image
                    ? \Illuminate\Support\Facades\Storage::url($this->template->background_image)
                    : null,
                'fields' => $this->template->fields,
            ]),
        ];
    }
}
