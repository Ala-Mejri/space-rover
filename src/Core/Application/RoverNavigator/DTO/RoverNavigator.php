<?php

declare(strict_types=1);

namespace App\Core\Application\RoverNavigator\DTO;

final readonly class RoverNavigator
{
    public function __construct(
        public int    $roverCurrentCoordinateX,
        public int    $roverCurrentCoordinateY,
        public string $orientationKey,
        public string $commandKeys,
    )
    {
    }
}