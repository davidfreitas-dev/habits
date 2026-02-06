<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\ValidationException;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JsonSerializable;

class Habit implements JsonSerializable
{
    private const MAX_TITLE_LENGTH = 255;

    private ?int $id = null;
    private string $title;
    private User $user;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, HabitWeekDay>
     */
    private Collection $habitWeekDays;

    public function __construct(
        string $title,
        User $user,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ) {
        $this->validateTitle($title);
        $this->title = $title;
        $this->user = $user;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
        $this->habitWeekDays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        if ($this->id !== null) {
            return;
        }
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->validateTitle($title);
        $this->title = $title;
        $this->touch();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Collection<int, HabitWeekDay>
     */
    public function getHabitWeekDays(): Collection
    {
        return $this->habitWeekDays;
    }

    public function addHabitWeekDay(HabitWeekDay $habitWeekDay): void
    {
        if (!$this->habitWeekDays->contains($habitWeekDay)) {
            $this->habitWeekDays->add($habitWeekDay);

            $this->touch();
        }
    }

    public function removeHabitWeekDay(HabitWeekDay $habitWeekDay): void
    {
        if ($this->habitWeekDays->removeElement($habitWeekDay)) {
            $this->touch();
        }
    }

    public function clearHabitWeekDays(): void
    {
        $this->habitWeekDays->clear();
        $this->touch();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'user_id' => $this->user->getId(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromArray(array $data, \App\Domain\Repository\UserRepositoryInterface $userRepository): self
    {
        $user = $userRepository->findById($data['user_id']);
        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User with ID %d not found.', $data['user_id']));
        }

        $habit = new self(
            $data['title'],
            $user,
            new DateTimeImmutable($data['created_at']),
            new DateTimeImmutable($data['updated_at']),
        );

        if (isset($data['id'])) {
            $habit->setId($data['id']);
        }

        return $habit;
    }

    private function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    private function validateTitle(string $title): void
    {
        $trimmedTitle = trim($title);

        if (empty($trimmedTitle)) {
            throw new ValidationException('Habit title cannot be empty.');
        }

        if (mb_strlen($trimmedTitle) > self::MAX_TITLE_LENGTH) {
            throw new ValidationException(
                sprintf('Habit title cannot exceed %d characters.', self::MAX_TITLE_LENGTH),
            );
        }
    }
}
