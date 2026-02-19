<?php

declare(strict_types=1);

namespace App\Application\DTO;

use JsonSerializable;

readonly class HabitStatsDayDTO implements JsonSerializable
{
    public function __construct(
        public string $date,
        public ?float $percentage,
        public int $completed,
        public int $total
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'date' => $this->date,
            'percentage' => $this->percentage,
            'completed' => $this->completed,
            'total' => $this->total,
        ];
    }
}
