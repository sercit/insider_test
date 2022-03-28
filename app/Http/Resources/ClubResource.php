<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClubResource extends JsonResource
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
            'name' => $this->name,
            'power' => $this->power,
            'wins' => $this->wins,
            'draws' => $this->draws,
            'loses' => $this->loses,
            'gd' => $this->gd,
            'points' => $this->points,
            'position' => $this->position,
        ];
    }
}
