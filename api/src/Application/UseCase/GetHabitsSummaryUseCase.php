<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\HabitSummaryResponseDTO;
use App\Domain\Repository\HabitRepositoryInterface;
use DateTimeImmutable;

class GetHabitsSummaryUseCase
{
    public function __construct(
        private readonly HabitRepositoryInterface $habitRepository,
    ) {
    }

    /**
     * @param int $userId
     * @return HabitSummaryResponseDTO
     */
    public function execute(int $userId, ?DateTimeImmutable $date = null): HabitSummaryResponseDTO
    {
        $currentDate = $date ?? new DateTimeImmutable();
        $summary = $this->habitRepository->getHabitSummary($userId, $currentDate);

        if ($summary === null) {
            return new HabitSummaryResponseDTO(
                date: $currentDate->format('Y-m-d'),
                completed: 0,
                total: 0,
            );
        }

        return new HabitSummaryResponseDTO(
            date: $summary['date'],
            completed: (int) $summary['completed'],
            total: (int) $summary['total'],
        );
    }
}
