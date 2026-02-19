<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use DateTimeImmutable;

interface HabitStatsRepositoryInterface
{
    /**
     * @return array<int, array{date: string, completed: int, total: int}>
     */
    public function getStatsByPeriod(int $userId, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array;
}
