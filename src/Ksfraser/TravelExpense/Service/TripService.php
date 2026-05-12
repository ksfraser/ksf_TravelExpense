<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Service;

use Ksfraser\TravelExpense\Entity\Trip;

class TripService
{
    private array $trips = [];

    public function create(array $data): Trip
    {
        $trip = Trip::fromArray($data);
        $trip->setId($data['id'] ?? uniqid('trip_'));
        $trip->setCreatedAt(new \DateTime());
        $trip->setUpdatedAt(new \DateTime());

        $this->trips[$trip->getId()] = $trip;

        return $trip;
    }

    public function get(string $id): ?Trip
    {
        return $this->trips[$id] ?? null;
    }

    public function update(string $id, array $data): Trip
    {
        $trip = $this->get($id);
        if (!$trip) {
            throw new \RuntimeException("Trip not found: {$id}");
        }

        foreach ($data as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($trip, $method)) {
                $trip->$method($value);
            }
        }
        $trip->setUpdatedAt(new \DateTime());

        return $trip;
    }

    public function delete(string $id): bool
    {
        if (!isset($this->trips[$id])) {
            return false;
        }
        unset($this->trips[$id]);
        return true;
    }

    public function findByEmployee(string $employeeId): array
    {
        return array_filter(
            $this->trips,
            fn(Trip $t) => $t->getEmployeeId() === $employeeId
        );
    }

    public function findByStatus(string $status): array
    {
        return array_filter(
            $this->trips,
            fn(Trip $t) => $t->getStatus() === $status
        );
    }

    public function findByProject(string $projectId): array
    {
        return array_filter(
            $this->trips,
            fn(Trip $t) => $t->getProjectId() === $projectId
        );
    }

    public function findOverBudget(): array
    {
        return array_filter(
            $this->trips,
            fn(Trip $t) => $t->isOverBudget()
        );
    }

    public function findInProgress(): array
    {
        return array_filter(
            $this->trips,
            fn(Trip $t) => $t->getStatus() === Trip::STATUS_IN_PROGRESS
        );
    }

    public function approve(string $tripId, string $approverId): Trip
    {
        $trip = $this->get($tripId);
        if (!$trip) {
            throw new \RuntimeException("Trip not found: {$tripId}");
        }
        $trip->approve($approverId);
        $trip->setUpdatedAt(new \DateTime());
        return $trip;
    }

    public function reject(string $tripId, string $reason): Trip
    {
        $trip = $this->get($tripId);
        if (!$trip) {
            throw new \RuntimeException("Trip not found: {$tripId}");
        }
        $trip->reject($reason);
        $trip->setUpdatedAt(new \DateTime());
        return $trip;
    }

    public function start(string $tripId): Trip
    {
        $trip = $this->get($tripId);
        if (!$trip) {
            throw new \RuntimeException("Trip not found: {$tripId}");
        }
        $trip->start();
        $trip->setUpdatedAt(new \DateTime());
        return $trip;
    }

    public function complete(string $tripId): Trip
    {
        $trip = $this->get($tripId);
        if (!$trip) {
            throw new \RuntimeException("Trip not found: {$tripId}");
        }
        $trip->complete();
        $trip->setUpdatedAt(new \DateTime());
        return $trip;
    }

    public function cancel(string $tripId): Trip
    {
        $trip = $this->get($tripId);
        if (!$trip) {
            throw new \RuntimeException("Trip not found: {$tripId}");
        }
        $trip->cancel();
        $trip->setUpdatedAt(new \DateTime());
        return $trip;
    }

    public function addExpense(string $tripId, float $amount): Trip
    {
        $trip = $this->get($tripId);
        if (!$trip) {
            throw new \RuntimeException("Trip not found: {$tripId}");
        }
        $trip->addExpense($amount);
        $trip->setUpdatedAt(new \DateTime());
        return $trip;
    }

    public function getAll(): array
    {
        return $this->trips;
    }

    public function search(string $query): array
    {
        $query = strtolower($query);
        return array_filter(
            $this->trips,
            fn(Trip $t) => 
                stripos($t->getName(), $query) !== false ||
                stripos($t->getDestination(), $query) !== false
        );
    }
}
