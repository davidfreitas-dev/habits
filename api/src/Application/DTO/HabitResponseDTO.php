<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entity\Habit;
use App\Domain\Entity\HabitWeekDay;
use JsonSerializable;

class HabitResponseDTO implements JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly array $weekDays,
        public readonly int $userId,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'week_days' => $this->weekDays,
            'user_id' => $this->userId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public static function fromEntity(Habit $habit): self
    {
        $weekDays = $habit->getHabitWeekDays()->map(fn (HabitWeekDay $hw) => $hw->getWeekDay())->toArray();

        return new self(
            id: $habit->getId(),
            title: $habit->getTitle(),
            weekDays: array_values($weekDays),
            userId: $habit->getUser()->getId(),
            createdAt: $habit->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $habit->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
