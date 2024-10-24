<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'surname' => $this->surname,
            'job' => $this->job,
            'profilePicture' => $this->profile_picture ? Storage::disk('public')->url('/profile-pictures/'.$this->profile_picture) : null,
            'disabled' => $this->disabled,
            'organization' => new OrganizationResource($this->organization),
            'role' => $this->role
        ];
    }
}
