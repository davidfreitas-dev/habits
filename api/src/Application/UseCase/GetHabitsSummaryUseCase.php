<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\HabitSummaryItemDTO;
use App\Application\DTO\HabitsSummaryResponseDTO;
use App\Domain\Repository\HabitRepositoryInterface;

class GetHabitsSummaryUseCase
{
    public function __construct(
        private readonly HabitRepositoryInterface $habitRepository,
    ) {
    }

    /**
     * @param int $userId
     * @return HabitsSummaryResponseDTO
     */
    public function execute(int $userId): HabitsSummaryResponseDTO
    {
        $summaryData = $this->habitRepository->getHabitsSummary($userId);
        
        $items = array_map(
            fn(array $item) => new HabitSummaryItemDTO(
                date: $item['date'],
                completed: (int) $item['completed'],
                total: (int) $item['total'],
            ),
            $summaryData
        );
        
        return new HabitsSummaryResponseDTO($items);
    }
}
