<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use DateTimeImmutable;

interface HabitStatsRepositoryInterface
{
    /**
     * @return array<int, array{week_day: int, completed: int, total: int}>
     */
    public function getWeekStats(int $userId, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array;

    /**
     * @return array<int, array{week_day: int, completed: int, total: int}>
     */
    public function getAggregatedStats(int $userId, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array;
}
