<?php

namespace App\DTO\V1;

class WeatherDTO
{
    public function __construct(
        private readonly string $city,
        private readonly ?string $country = null,
        private readonly ?float $temperature = null,
        private readonly ?string $description = null,
        private readonly ?int $humidity = null,
        private readonly ?float $windSpeed = null,
        private readonly ?array $requestData = null,
        private readonly ?array $responseData = null
    ) {
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getHumidity(): ?int
    {
        return $this->humidity;
    }

    public function getWindSpeed(): ?float
    {
        return $this->windSpeed;
    }

    public function getRequestData(): ?array
    {
        return $this->requestData;
    }

    public function getResponseData(): ?array
    {
        return $this->responseData;
    }

    public function toArray(): array
    {
        return [
            'city' => $this->city,
            'country' => $this->country,
            'temperature' => $this->temperature,
            'description' => $this->description,
            'humidity' => $this->humidity,
            'wind_speed' => $this->windSpeed,
            'request_data' => $this->requestData,
            'response_data' => $this->responseData,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            city: $data['city'],
            country: $data['country'] ?? null,
            temperature: $data['temperature'] ?? null,
            description: $data['description'] ?? null,
            humidity: $data['humidity'] ?? null,
            windSpeed: $data['wind_speed'] ?? null,
            requestData: $data['request_data'] ?? null,
            responseData: $data['response_data'] ?? null
        );
    }
}
