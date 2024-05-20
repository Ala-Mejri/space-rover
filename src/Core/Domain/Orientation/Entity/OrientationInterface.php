<?php

declare(strict_types=1);

namespace App\Core\Domain\Orientation\Entity;

use App\Core\Domain\Orientation\Enum\OrientationType;
use App\Shared\Domain\Coordinate\ValueObject\Coordinate;

interface OrientationInterface
{
    public function left(): OrientationInterface;

    public function right(): OrientationInterface;

    public function moveForward(): Coordinate;

    public function getAbbreviation(): OrientationType;
}