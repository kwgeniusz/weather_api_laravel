<?php

namespace App\DTO\V1;

class FavoriteDTO
{
    public function __construct(
        private readonly ?int $id = null,
        private readonly ?int $userId = null,
        private readonly string $city,
        private readonly ?string $country = null,
        private readonly ?float $latitude = null,
        private readonly ?float $longitude = null,
        private readonly bool $isDefault = false
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'city' => $this->city,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_default' => $this->isDefault,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            userId: $data['user_id'] ?? null,
            city: $data['city'],
            country: $data['country'] ?? null,
            latitude: $data['latitude'] ?? null,
            longitude: $data['longitude'] ?? null,
            isDefault: $data['is_default'] ?? false
        );
    }
}
