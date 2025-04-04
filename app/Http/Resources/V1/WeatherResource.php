<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeatherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'city' => $this->city,
            'country' => $this->country,
            'temperature' => $this->temperature,
            'description' => $this->description,
            'humidity' => $this->humidity,
            'wind_speed' => $this->wind_speed,
            'local_time' => $this->local_time,
            'created_at' => $this->created_at,
        ];
    }
}
