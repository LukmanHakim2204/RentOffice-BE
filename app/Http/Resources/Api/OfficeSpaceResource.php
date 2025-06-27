<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
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
            'about' => $this->about,
            'city_id' => new CityResource($this->whenLoaded('city')),
            'officeSpacePhotos' => OfficeSpacePhotoResource::collection($this->whenLoaded('officeSpacePhotos')),
            'officeSpaceBenefits' => OfficeSpaceBenefitResource::collection($this->whenLoaded('officeSpaceBenefits')),
        ];
    }
}
