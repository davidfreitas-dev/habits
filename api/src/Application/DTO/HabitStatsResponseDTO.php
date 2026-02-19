<?php

declare(strict_types=1);

namespace App\Application\DTO;

use JsonSerializable;

readonly class HabitStatsResponseDTO implements JsonSerializable
{
    /**
     * @param HabitStatsDayDTO[] $data
     */
    public function __construct(
        public array $data
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'data' => $this->data,
        ];
    }
}
