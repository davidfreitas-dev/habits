<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\Seeders\DaySeeder;
use App\Infrastructure\Persistence\Seeders\HabitSeeder;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// Load .env file
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// Add settings to the container builder
$settings = require __DIR__ . '/../config/settings.php';
$containerBuilder->addDefinitions($settings);

// Add dependencies to the container builder
$dependencies = require __DIR__ . '/../config/container.php';
$containerBuilder->addDefinitions($dependencies);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Get the PDO instance
$pdo = $container->get(PDO::class);

// Run the DaySeeder
$daySeeder = new DaySeeder($pdo);
$daySeeder->run();

// Run the HabitSeeder
$habitSeeder = new HabitSeeder(
    $pdo,
    $container->get(App\Domain\Repository\UserRepositoryInterface::class),
    $container->get(App\Domain\Repository\HabitRepositoryInterface::class)
);
$habitSeeder->run();

echo "All seeders executed successfully!\n";
