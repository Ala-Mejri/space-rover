<?php

declare(strict_types=1);

namespace App\Core\Domain\Orientation\Entity;

use App\Core\Domain\Orientation\Enum\OrientationType;
use App\Shared\Domain\Coordinate\ValueObject\Coordinate;

final class SouthOrientation implements OrientationInterface
{
    public function left(): OrientationInterface
    {
        return new EastOrientation();
    }

    public function right(): OrientationInterface
    {
        return new WestOrientation();
    }

    public function moveForward(): Coordinate
    {
        return new Coordinate(0, -1);
    }

    public function getAbbreviation(): OrientationType
    {
        return OrientationType::SOUTH;
    }
}