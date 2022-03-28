<?php

namespace App\Http\Resources;

use App\Models\Club;
use Illuminate\Http\Resources\Json\JsonResource;

class FixtureResource extends JsonResource
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
            'tournament_id' => $this->tournament_id,
            'week_id' => $this->week_id,
            'matches' => MatchResource::collection($this->matches),
        ];
    }
}
