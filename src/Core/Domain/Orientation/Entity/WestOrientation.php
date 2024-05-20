<?php

declare(strict_types=1);

namespace App\Core\Domain\Orientation\Entity;

use App\Core\Domain\Orientation\Enum\OrientationType;
use App\Shared\Domain\Coordinate\ValueObject\Coordinate;

final class WestOrientation implements OrientationInterface
{
    public function left(): OrientationInterface
    {
        return new SouthOrientation();
    }

    public function right(): OrientationInterface
    {
        return new NorthOrientation();
    }

    public function moveForward(): Coordinate
    {
        return new Coordinate(-1, 0);
    }

    public function getAbbreviation(): OrientationType
    {
        return OrientationType::WEST;
    }
}