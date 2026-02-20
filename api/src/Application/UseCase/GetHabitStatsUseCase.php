<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\HabitStatsResponseDTO;
use App\Application\DTO\HabitStatsWeekDayDTO;
use App\Domain\Repository\HabitStatsRepositoryInterface;
use DateTimeImmutable;
use InvalidArgumentException;

readonly class GetHabitStatsUseCase
{
    private const LABELS = [
        0 => 'D',
        1 => 'S',
        2 => 'T',
        3 => 'Q',
        4 => 'Q',
        5 => 'S',
        6 => 'S',
    ];

    public function __construct(
        private HabitStatsRepositoryInterface $habitStatsRepository,
    ) {
    }

    public function execute(int $userId, string $period): HabitStatsResponseDTO
    {
        $endDate = new DateTimeImmutable('today');
        $startDate = $this->calculateStartDate($period, $endDate);

        $stats = $period === 'W'
            ? $this->habitStatsRepository->getWeekStats($userId, $startDate, $endDate)
            : $this->habitStatsRepository->getAggregatedStats($userId, $startDate, $endDate);

        $statsByWeekDay = [];
        foreach ($stats as $row) {
            $statsByWeekDay[(int) $row['week_day']] = $row;
        }

        $dtos = [];
        for ($i = 0; $i <= 6; $i++) {
            $row = $statsByWeekDay[$i] ?? ['completed' => 0, 'total' => 0];

            $total = (int) $row['total'];
            $completed = (int) $row['completed'];

            $percentage = $total > 0
                ? round(($completed / $total) * 100, 2)
                : null;

            $dtos[] = new HabitStatsWeekDayDTO(
                $i,
                self::LABELS[$i],
                $percentage,
                $completed,
                $total,
            );
        }

        $streaks = $this->habitStatsRepository->getStreaks($userId);

        return new HabitStatsResponseDTO(
            $dtos,
            $streaks['current_streak'],
            $streaks['longest_streak'],
        );
    }

    private function calculateStartDate(string $period, DateTimeImmutable $endDate): DateTimeImmutable
    {
        return match ($period) {
            'W' => $endDate->modify('last sunday'),
            'M' => $endDate->modify('-29 days'),
            '3M' => $endDate->modify('-89 days'),
            '6M' => $endDate->modify('-179 days'),
            'Y' => $endDate->modify('-364 days'),
            default => throw new InvalidArgumentException("Invalid period: {$period}")
        };
    }
}
