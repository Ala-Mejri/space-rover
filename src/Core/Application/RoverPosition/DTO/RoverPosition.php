<?php

declare(strict_types=1);

namespace App\Core\Application\RoverPosition\DTO;

final readonly class RoverPosition
{
    public function __construct(public string $value)
    {
    }
}