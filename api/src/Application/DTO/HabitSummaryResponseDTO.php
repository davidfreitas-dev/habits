<?php

declare(strict_types=1);

namespace App\Application\DTO;

use JsonSerializable;

class HabitSummaryResponseDTO implements JsonSerializable
{
    public function __construct(
        public readonly string $date,
        public readonly int $completed,
        public readonly int $total,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'date' => $this->date,
            'completed' => $this->completed,
            'total' => $this->total,
        ];
    }
}
