<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\HabitResponseDTO;
use App\Application\DTO\HabitsByDayRequestDTO;
use App\Application\DTO\HabitsByDayResponseDTO;
use App\Application\Service\ValidationService;
use App\Domain\Entity\Habit;
use App\Domain\Repository\DayRepositoryInterface;
use App\Domain\Repository\HabitRepositoryInterface;
use DateTimeImmutable;

class GetHabitsByDayUseCase
{
    public function __construct(
        private readonly ValidationService $validationService,
        private readonly HabitRepositoryInterface $habitRepository,
        private readonly DayRepositoryInterface $dayRepository,
    ) {
    }

    public function execute(HabitsByDayRequestDTO $dto, int $userId): HabitsByDayResponseDTO
    {
        $this->validationService->validate($dto);

        $date = new DateTimeImmutable($dto->date);

        $possibleHabits = array_map(
            static fn (Habit $habit): HabitResponseDTO => HabitResponseDTO::fromEntity($habit),
            $this->habitRepository->findPossibleHabits($date, $userId),
        );

        $completedHabitsEntities = $this->habitRepository->findCompletedHabits($date, $userId);
        $completedHabits = array_map(
            static fn (Habit $habit): HabitResponseDTO => HabitResponseDTO::fromEntity($habit),
            $completedHabitsEntities,
        );

        return new HabitsByDayResponseDTO(
            possibleHabits: $possibleHabits,
            completedHabits: $completedHabits,
        );
    }
}
