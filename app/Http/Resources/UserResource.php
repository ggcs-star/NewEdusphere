<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'biography' => $this->biography,
            'short_title' => $this->short_title,
            'avatar_url' => $this->avatarUrl(),
            'social_links' => $this->socialLinks(),
        ];
    }
}
