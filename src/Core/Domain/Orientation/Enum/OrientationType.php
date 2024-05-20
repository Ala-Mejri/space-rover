<?php

declare(strict_types=1);

namespace App\Core\Domain\Orientation\Enum;

use App\Core\Domain\Orientation\Entity\EastOrientation;
use App\Core\Domain\Orientation\Entity\NorthOrientation;
use App\Core\Domain\Orientation\Entity\OrientationInterface;
use App\Core\Domain\Orientation\Entity\SouthOrientation;
use App\Core\Domain\Orientation\Entity\WestOrientation;

enum OrientationType: string
{
    case NORTH = 'N';
    case EAST = 'E';
    case SOUTH = 'S';
    case WEST = 'W';

    public function getOrientation(): OrientationInterface
    {
        return match ($this) {
            self::NORTH => new NorthOrientation(),
            self::EAST => new EastOrientation(),
            self::SOUTH => new SouthOrientation(),
            self::WEST => new WestOrientation(),
        };
    }
}
