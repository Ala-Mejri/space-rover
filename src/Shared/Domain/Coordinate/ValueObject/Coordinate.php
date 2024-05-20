<?php

declare(strict_types=1);

namespace App\Shared\Domain\Coordinate\ValueObject;

final readonly class Coordinate
{
    public function __construct(private int $x, private int $y)
    {
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }
}