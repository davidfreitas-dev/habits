<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Persistence\Repository;

use App\Infrastructure\Persistence\MySQL\PersonRepository;
use App\Infrastructure\Persistence\MySQL\RoleRepository;
use App\Infrastructure\Persistence\MySQL\UserRepository;
use App\Infrastructure\Persistence\Repository\HabitRepository;
use App\Infrastructure\Persistence\Repository\HabitStatsRepository;
use App\Domain\Entity\User;
use App\Domain\Entity\Habit;
use App\Domain\Entity\Person;
use DateTimeImmutable;
use Tests\Integration\DatabaseTestCase;

class HabitStatsRepositoryTest extends DatabaseTestCase
{
    private HabitStatsRepository $habitStatsRepository;
    private HabitRepository $habitRepository;
    private UserRepository $userRepository;
    private PersonRepository $personRepository;
    private RoleRepository $roleRepository;
    private User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->personRepository = new PersonRepository(self::$pdo);
        $this->roleRepository = new RoleRepository(self::$pdo);
        $this->userRepository = new UserRepository(self::$pdo, $this->personRepository, $this->roleRepository);
        $this->habitRepository = new HabitRepository(self::$pdo, $this->userRepository);
        $this->habitStatsRepository = new HabitStatsRepository(self::$pdo);

        $this->testUser = $this->createTestUser('Stats User', 'stats_repo@example.com');
    }

    public function testFetchStatsReturnsCorrectData(): void
    {
        // Sunday, Feb 15 to Thursday, Feb 19 (5 days)
        $startDate = new DateTimeImmutable('2026-02-15');
        $endDate = new DateTimeImmutable('2026-02-19');

        // Create a daily habit
        $habit = new Habit('Daily Habit', $this->testUser, new DateTimeImmutable('2026-02-01'));
        $createdHabit = $this->habitRepository->create($habit, [0, 1, 2, 3, 4, 5, 6]);

        // Complete it on Monday (Feb 16) and Wednesday (Feb 18)
        $dayMondayId = $this->ensureDayExists(new DateTimeImmutable('2026-02-16'));
        $dayWednesdayId = $this->ensureDayExists(new DateTimeImmutable('2026-02-18'));
        
        $this->markHabitAsCompleted($dayMondayId, $createdHabit->getId());
        $this->markHabitAsCompleted($dayWednesdayId, $createdHabit->getId());

        $stats = $this->habitStatsRepository->getWeekStats($this->testUser->getId(), $startDate, $endDate);

        // Expected week_days in range: 0 (Sun), 1 (Mon), 2 (Tue), 3 (Wed), 4 (Thu)
        $this->assertCount(5, $stats);

        $statsMap = [];
        foreach ($stats as $row) {
            $statsMap[(int)$row['week_day']] = $row;
        }

        // Sunday (0)
        $this->assertEquals(0, $statsMap[0]['completed']);
        $this->assertEquals(1, $statsMap[0]['total']);

        // Monday (1)
        $this->assertEquals(1, $statsMap[1]['completed']);
        $this->assertEquals(1, $statsMap[1]['total']);

        // Tuesday (2)
        $this->assertEquals(0, $statsMap[2]['completed']);
        $this->assertEquals(1, $statsMap[2]['total']);

        // Wednesday (3)
        $this->assertEquals(1, $statsMap[3]['completed']);
        $this->assertEquals(1, $statsMap[3]['total']);

        // Thursday (4)
        $this->assertEquals(0, $statsMap[4]['completed']);
        $this->assertEquals(1, $statsMap[4]['total']);
    }

    private function createTestUser(string $name, string $email): User
    {
        $person = new Person(name: $name, email: $email);
        $createdPerson = $this->personRepository->create($person);
        
        $customerRole = $this->roleRepository->findByName('customer');
        $user = new User(person: $createdPerson, role: $customerRole, password: 'password');
        $this->userRepository->create($user);
        
        return $this->userRepository->findByEmail($email);
    }

    private function ensureDayExists(DateTimeImmutable $date): int
    {
        $stmt = self::$pdo->prepare('INSERT IGNORE INTO days (date) VALUES (:date)');
        $stmt->execute(['date' => $date->format('Y-m-d')]);
        
        $stmt = self::$pdo->prepare('SELECT id FROM days WHERE date = :date');
        $stmt->execute(['date' => $date->format('Y-m-d')]);
        
        return (int)$stmt->fetchColumn();
    }

    private function markHabitAsCompleted(int $dayId, int $habitId): void
    {
        $stmt = self::$pdo->prepare('INSERT INTO day_habits (day_id, habit_id) VALUES (:day_id, :habit_id)');
        $stmt->execute(['day_id' => $dayId, 'habit_id' => $habitId]);
    }
}
