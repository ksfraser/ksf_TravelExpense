<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Service;

use Ksfraser\TravelExpense\Entity\Supplier;

class SupplierService
{
    private array $suppliers = [];

    public function create(array $data): Supplier
    {
        $supplier = Supplier::fromArray($data);
        $supplier->setId($data['id'] ?? uniqid('sup_'));
        $supplier->setCreatedAt(new \DateTime());
        $supplier->setUpdatedAt(new \DateTime());

        $this->suppliers[$supplier->getId()] = $supplier;

        return $supplier;
    }

    public function get(string $id): ?Supplier
    {
        return $this->suppliers[$id] ?? null;
    }

    public function update(string $id, array $data): Supplier
    {
        $supplier = $this->get($id);
        if (!$supplier) {
            throw new \RuntimeException("Supplier not found: {$id}");
        }

        foreach ($data as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($supplier, $method)) {
                $supplier->$method($value);
            }
        }
        $supplier->setUpdatedAt(new \DateTime());

        return $supplier;
    }

    public function delete(string $id): bool
    {
        if (!isset($this->suppliers[$id])) {
            return false;
        }
        unset($this->suppliers[$id]);
        return true;
    }

    public function findByType(string $type): array
    {
        return array_filter(
            $this->suppliers,
            fn(Supplier $s) => $s->getType() === $type
        );
    }

    public function findActiveByType(string $type): array
    {
        return array_filter(
            $this->findByType($type),
            fn(Supplier $s) => $s->isActive()
        );
    }

    public function findPreferred(string $type): array
    {
        $active = $this->findActiveByType($type);
        $preferred = array_filter(
            $active,
            fn(Supplier $s) => $s->isPreferred()
        );

        usort($preferred, fn(Supplier $a, Supplier $b) => 
            $a->getPreferenceOrder() <=> $b->getPreferenceOrder()
        );

        return $preferred;
    }

    public function findWithCorporateRate(string $type): array
    {
        return array_filter(
            $this->findActiveByType($type),
            fn(Supplier $s) => $s->hasCorporateRate()
        );
    }

    public function activate(string $id): Supplier
    {
        $supplier = $this->get($id);
        if (!$supplier) {
            throw new \RuntimeException("Supplier not found: {$id}");
        }
        $supplier->activate();
        $supplier->setUpdatedAt(new \DateTime());
        return $supplier;
    }

    public function deactivate(string $id): Supplier
    {
        $supplier = $this->get($id);
        if (!$supplier) {
            throw new \RuntimeException("Supplier not found: {$id}");
        }
        $supplier->deactivate();
        $supplier->setUpdatedAt(new \DateTime());
        return $supplier;
    }

    public function getAll(): array
    {
        return $this->suppliers;
    }

    public function getActiveAll(): array
    {
        return array_filter(
            $this->suppliers,
            fn(Supplier $s) => $s->isActive()
        );
    }

    public function search(string $query): array
    {
        $query = strtolower($query);
        return array_filter(
            $this->suppliers,
            fn(Supplier $s) => 
                stripos($s->getName(), $query) !== false ||
                stripos($s->getType(), $query) !== false
        );
    }
}
