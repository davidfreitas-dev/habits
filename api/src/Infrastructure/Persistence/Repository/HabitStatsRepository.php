<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Repository\HabitStatsRepositoryInterface;
use DateTimeImmutable;
use PDO;

class HabitStatsRepository implements HabitStatsRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function getWeekStats(int $userId, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array
    {
        return $this->fetchStats($userId, $startDate, $endDate);
    }

    public function getAggregatedStats(int $userId, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array
    {
        return $this->fetchStats($userId, $startDate, $endDate);
    }

    /**
     * @return array<int, array{week_day: int, completed: int, total: int}>
     */
    private function fetchStats(int $userId, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array
    {
        $sql = "
            WITH RECURSIVE dates AS (
                SELECT :start_date AS date
                UNION ALL
                SELECT date + INTERVAL 1 DAY
                FROM dates
                WHERE date < :end_date
            ),
            day_stats AS (
                SELECT 
                    d.date,
                    (WEEKDAY(d.date) + 1) % 7 as week_day,
                    COUNT(DISTINCT CASE WHEN dh.id IS NOT NULL THEN hwd.habit_id END) as completed,
                    COUNT(DISTINCT hwd.id) as total
                FROM dates d
                CROSS JOIN (SELECT :user_id as user_id) u
                LEFT JOIN habits h ON h.user_id = u.user_id 
                    AND DATE(h.created_at) <= d.date
                LEFT JOIN habit_week_days hwd ON hwd.habit_id = h.id 
                    AND hwd.week_day = (WEEKDAY(d.date) + 1) % 7
                LEFT JOIN days ds ON ds.date = d.date
                LEFT JOIN day_habits dh ON dh.day_id = ds.id AND dh.habit_id = hwd.habit_id
                GROUP BY d.date
            )
            SELECT 
                week_day,
                SUM(completed) as completed,
                SUM(total) as total
            FROM day_stats
            GROUP BY week_day
            ORDER BY week_day ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'user_id' => $userId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
