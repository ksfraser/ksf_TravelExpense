<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Entity;

class Supplier
{
    public const TYPE_HOTEL = 'hotel';
    public const TYPE_CAR_RENTAL = 'car_rental';
    public const TYPE_TAXI = 'taxi';
    public const TYPE_TRANSIT = 'transit';
    public const TYPE_MEAL = 'meal';
    public const TYPE_OTHER = 'other';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    private ?string $id = null;
    private string $name = '';
    private string $type = self::TYPE_OTHER;
    private string $contact = '';
    private string $website = '';
    private ?string $rateCode = null;
    private int $preferenceOrder = 999;
    private bool $corporateRateAvailable = false;
    private string $status = self::STATUS_ACTIVE;
    private ?\DateTime $createdAt = null;
    private ?\DateTime $updatedAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getContact(): string
    {
        return $this->contact;
    }

    public function setContact(string $contact): self
    {
        $this->contact = $contact;
        return $this;
    }

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function setWebsite(string $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getRateCode(): ?string
    {
        return $this->rateCode;
    }

    public function setRateCode(?string $rateCode): self
    {
        $this->rateCode = $rateCode;
        return $this;
    }

    public function getPreferenceOrder(): int
    {
        return $this->preferenceOrder;
    }

    public function setPreferenceOrder(int $preferenceOrder): self
    {
        $this->preferenceOrder = $preferenceOrder;
        return $this;
    }

    public function isPreferred(): bool
    {
        return $this->preferenceOrder <= 3;
    }

    public function hasCorporateRate(): bool
    {
        return $this->corporateRateAvailable;
    }

    public function setCorporateRateAvailable(bool $available): self
    {
        $this->corporateRateAvailable = $available;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function activate(): self
    {
        $this->status = self::STATUS_ACTIVE;
        return $this;
    }

    public function deactivate(): self
    {
        $this->status = self::STATUS_INACTIVE;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'contact' => $this->contact,
            'website' => $this->website,
            'rate_code' => $this->rateCode,
            'preference_order' => $this->preferenceOrder,
            'corporate_rate_available' => $this->corporateRateAvailable,
            'status' => $this->status,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromArray(array $data): self
    {
        $supplier = new self();
        
        if (isset($data['id'])) $supplier->setId($data['id']);
        if (isset($data['name'])) $supplier->setName($data['name']);
        if (isset($data['type'])) $supplier->setType($data['type']);
        if (isset($data['contact'])) $supplier->setContact($data['contact']);
        if (isset($data['website'])) $supplier->setWebsite($data['website']);
        if (isset($data['rate_code'])) $supplier->setRateCode($data['rate_code']);
        if (isset($data['preference_order'])) $supplier->setPreferenceOrder($data['preference_order']);
        if (isset($data['corporate_rate_available'])) $supplier->setCorporateRateAvailable((bool)$data['corporate_rate_available']);
        if (isset($data['status'])) $supplier->setStatus($data['status']);
        
        return $supplier;
    }
}