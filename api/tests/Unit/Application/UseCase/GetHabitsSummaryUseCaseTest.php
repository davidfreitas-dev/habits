<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCase;

use App\Application\DTO\HabitSummaryResponseDTO;
use App\Application\UseCase\GetHabitsSummaryUseCase;
use App\Domain\Repository\HabitRepositoryInterface;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class GetHabitsSummaryUseCaseTest extends TestCase
{
    private HabitRepositoryInterface&MockObject $habitRepository;
    private GetHabitsSummaryUseCase $getHabitsSummaryUseCase;

    public function testShouldReturnHabitSummarySuccessfully(): void
    {
        $userId = 1;
        $fixedDate = new DateTimeImmutable('2024-02-07 10:00:00'); // Use a fixed date for testability
        $summaryData = [
            'date' => $fixedDate->format('Y-m-d'),
            'completed' => 5,
            'total' => 10,
        ];

        $this->habitRepository->expects($this->once())->method('getHabitSummary')->with($userId, $fixedDate)->willReturn($summaryData);

        $response = $this->getHabitsSummaryUseCase->execute($userId, $fixedDate);

        $this->assertInstanceOf(HabitSummaryResponseDTO::class, $response);
        $this->assertEquals($fixedDate->format('Y-m-d'), $response->date);
        $this->assertEquals(5, $response->completed);
        $this->assertEquals(10, $response->total);
    }

    public function testShouldReturnZeroSummaryIfNoSummaryFound(): void
    {
        $userId = 1;
        $fixedDate = new DateTimeImmutable('2024-02-07 10:00:00'); // Use a fixed date for testability

        $this->habitRepository->expects($this->once())->method('getHabitSummary')->with($userId, $fixedDate)->willReturn(null);

        $response = $this->getHabitsSummaryUseCase->execute($userId, $fixedDate);

        $this->assertInstanceOf(HabitSummaryResponseDTO::class, $response);
        $this->assertEquals($fixedDate->format('Y-m-d'), $response->date);
        $this->assertEquals(0, $response->completed);
        $this->assertEquals(0, $response->total);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->habitRepository = $this->createMock(HabitRepositoryInterface::class);

        $this->getHabitsSummaryUseCase = new GetHabitsSummaryUseCase(
            $this->habitRepository,
        );
    }
}
