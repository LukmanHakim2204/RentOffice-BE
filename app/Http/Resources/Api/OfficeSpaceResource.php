<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use App\Http\Resources\Api\ContactResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OfficeSpaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'duration' => $this->duration,
            'thumbnail' => $this->thumbnail,
            'address' => $this->address,
            'about' => $this->about,
            'city' => new CityResource($this->whenLoaded('city')),
            'photos' => OfficeSpacePhotoResource::collection($this->whenLoaded('photos')),
            'benefits' => OfficeSpaceBenefitResource::collection($this->whenLoaded('benefits')),
            'contacts' => ContactResource::collection($this->whenLoaded('contacts'))
        ];
    }
}
