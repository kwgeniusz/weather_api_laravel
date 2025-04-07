<?php

namespace App\DTO\V1;

class FavoriteDTO
{
    public function __construct(
        private ?int $id = null,
        private ?int $userId = null,
        private string $city,
        private ?string $country = null,
        private ?float $latitude = null,
        private ?float $longitude = null,
        private bool $isDefault = false
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

    public function setDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;
        return $this;
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
            $data['id'] ?? null,
            $data['user_id'] ?? null,
            $data['city'],
            $data['country'] ?? null,
            $data['latitude'] ?? null,
            $data['longitude'] ?? null,
            $data['is_default'] ?? false
        );
    }
}
