<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
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
            'home_club' => new ClubResource($this->homeClub),
            'away_club' => new ClubResource($this->awayClub),
            'home_club_goals' => $this->home_club_goals,
            'away_club_goals' => $this->away_club_goals,
        ];
    }
}
