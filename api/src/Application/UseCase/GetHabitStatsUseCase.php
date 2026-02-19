<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\HabitStatsDayDTO;
use App\Application\DTO\HabitStatsResponseDTO;
use App\Domain\Repository\HabitStatsRepositoryInterface;
use DateTimeImmutable;
use InvalidArgumentException;

readonly class GetHabitStatsUseCase
{
    public function __construct(
        private HabitStatsRepositoryInterface $habitStatsRepository
    ) {}

    public function execute(int $userId, string $period): HabitStatsResponseDTO
    {
        $endDate = new DateTimeImmutable('today');
        $startDate = $this->calculateStartDate($period, $endDate);

        $stats = $this->habitStatsRepository->getStatsByPeriod($userId, $startDate, $endDate);

        $dtos = array_map(function (array $day) {
            $total = (int) $day['total'];
            $completed = (int) $day['completed'];
            
            $percentage = $total > 0 
                ? round(($completed / $total) * 100, 2) 
                : null;

            return new HabitStatsDayDTO(
                $day['date'],
                $percentage,
                $completed,
                $total
            );
        }, $stats);

        return new HabitStatsResponseDTO($dtos);
    }

    private function calculateStartDate(string $period, DateTimeImmutable $endDate): DateTimeImmutable
    {
        return match ($period) {
            'W' => $endDate->modify('-6 days'),
            'M' => $endDate->modify('-29 days'),
            '3M' => $endDate->modify('-89 days'),
            '6M' => $endDate->modify('-179 days'),
            'Y' => $endDate->modify('-364 days'),
            default => throw new InvalidArgumentException("Invalid period: {$period}")
        };
    }
}
