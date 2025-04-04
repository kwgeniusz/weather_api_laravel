<?php

namespace App\DTO\V1;

class WeatherDTO
{
    public function __construct(
        private string $city,
        private ?string $country = null,
        private ?float $temperature = null,
        private ?string $description = null,
        private ?int $humidity = null,
        private ?float $windSpeed = null,
        private array $requestData = [],
        private array $responseData = []
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

    public function getRequestData(): array
    {
        return $this->requestData;
    }

    public function getResponseData(): array
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
            $data['city'],
            $data['country'] ?? null,
            $data['temperature'] ?? null,
            $data['description'] ?? null,
            $data['humidity'] ?? null,
            $data['wind_speed'] ?? null,
            $data['request_data'] ?? [],
            $data['response_data'] ?? []
        );
    }
}
