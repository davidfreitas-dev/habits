<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCase;

use App\Application\UseCase\GetHabitStatsUseCase;
use App\Domain\Repository\HabitStatsRepositoryInterface;
use App\Application\DTO\HabitStatsResponseDTO;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;
use DateTimeImmutable;
use InvalidArgumentException;

class GetHabitStatsUseCaseTest extends TestCase
{
    private HabitStatsRepositoryInterface&MockObject $habitStatsRepository;
    private GetHabitStatsUseCase $getHabitStatsUseCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->habitStatsRepository = $this->createMock(HabitStatsRepositoryInterface::class);
        $this->getHabitStatsUseCase = new GetHabitStatsUseCase($this->habitStatsRepository);
    }

    public function testShouldReturnStatsSuccessfullyForWeeklyPeriod(): void
    {
        $userId = 1;
        $period = 'W';
        
        $mockStats = [
            ['week_day' => 1, 'completed' => 2, 'total' => 3],
            ['week_day' => 3, 'completed' => 1, 'total' => 1],
        ];

        $this->habitStatsRepository->expects($this->once())
            ->method('getWeekStats')
            ->with($userId, $this->isInstanceOf(DateTimeImmutable::class), $this->isInstanceOf(DateTimeImmutable::class))
            ->willReturn($mockStats);

        $response = $this->getHabitStatsUseCase->execute($userId, $period);

        $this->assertInstanceOf(HabitStatsResponseDTO::class, $response);
        $this->assertCount(7, $response->data);
        
        // Monday (1)
        $this->assertEquals(1, $response->data[1]->weekDay);
        $this->assertEquals('S', $response->data[1]->label);
        $this->assertEquals(66.67, $response->data[1]->percentage);
        $this->assertEquals(2, $response->data[1]->completed);
        $this->assertEquals(3, $response->data[1]->total);

        // Wednesday (3)
        $this->assertEquals(3, $response->data[3]->weekDay);
        $this->assertEquals('Q', $response->data[3]->label);
        $this->assertEquals(100.0, $response->data[3]->percentage);
        $this->assertEquals(1, $response->data[3]->completed);
        $this->assertEquals(1, $response->data[3]->total);

        // Sunday (0) - No data
        $this->assertEquals(0, $response->data[0]->weekDay);
        $this->assertNull($response->data[0]->percentage);
        $this->assertEquals(0, $response->data[0]->completed);
        $this->assertEquals(0, $response->data[0]->total);
    }

    public function testShouldReturnStatsSuccessfullyForMonthlyPeriod(): void
    {
        $userId = 1;
        $period = 'M';
        
        $mockStats = [
            ['week_day' => 0, 'completed' => 10, 'total' => 20],
        ];

        $this->habitStatsRepository->expects($this->once())
            ->method('getAggregatedStats')
            ->with($userId, $this->isInstanceOf(DateTimeImmutable::class), $this->isInstanceOf(DateTimeImmutable::class))
            ->willReturn($mockStats);

        $response = $this->getHabitStatsUseCase->execute($userId, $period);

        $this->assertInstanceOf(HabitStatsResponseDTO::class, $response);
        $this->assertEquals(50.0, $response->data[0]->percentage);
    }

    public function testShouldThrowExceptionForInvalidPeriod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid period: INVALID');

        $this->getHabitStatsUseCase->execute(1, 'INVALID');
    }
}
