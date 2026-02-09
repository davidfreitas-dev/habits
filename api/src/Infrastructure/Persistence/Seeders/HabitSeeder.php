<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Seeders;

use App\Domain\Entity\Habit;
use App\Domain\Repository\HabitRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Faker\Factory;
use PDO;

class HabitSeeder
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly UserRepositoryInterface $userRepository,
        private readonly HabitRepositoryInterface $habitRepository,
    ) {
    }

    public function run(): void
    {
        $faker = Factory::create('pt_BR');

        // Fetch all users
        $users = $this->userRepository->findAll();

        if (empty($users)) {
            echo "No users found. Please ensure users are seeded first.
";
            return;
        }

        $this->pdo->beginTransaction();
        try {
            foreach ($users as $user) {
                // Create 3 to 5 random habits for each user
                $numberOfHabits = random_int(3, 5);
                for ($i = 0; $i < $numberOfHabits; $i++) {
                    $title = $faker->sentence(3, true);
                    $weekDays = $this->generateRandomWeekDays();

                    $habit = new Habit(
                        user: $user,
                        title: $title,
                        createdAt: new DateTimeImmutable(),
                        updatedAt: new DateTimeImmutable(),
                    );

                    $this->habitRepository->create($habit, $weekDays);
                }
            }
            $this->pdo->commit();
            echo "Habits seeded successfully!
";
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function generateRandomWeekDays(): array
    {
        $weekDays = [];
        $numberOfDays = random_int(1, 7); // At least 1 day, up to 7
        $allDays = range(0, 6); // 0=Sunday, 6=Saturday
        shuffle($allDays);

        for ($i = 0; $i < $numberOfDays; $i++) {
            $weekDays[] = $allDays[$i];
        }
        sort($weekDays);
        return $weekDays;
    }
}
