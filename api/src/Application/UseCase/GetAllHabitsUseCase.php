<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\HabitResponseDTO;
use App\Domain\Entity\Habit;
use App\Domain\Repository\HabitRepositoryInterface;

class GetAllHabitsUseCase
{
    public function __construct(
        private readonly HabitRepositoryInterface $habitRepository,
    ) {
    }

    /**
     * @return HabitResponseDTO[]
     */
    public function execute(int $userId): array
    {
        $habits = $this->habitRepository->findAllByUserId($userId);

        return array_map(
            static fn (Habit $habit): HabitResponseDTO => HabitResponseDTO::fromEntity($habit),
            $habits,
        );
    }
}
