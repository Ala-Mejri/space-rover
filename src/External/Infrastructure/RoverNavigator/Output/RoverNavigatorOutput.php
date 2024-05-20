<?php

declare(strict_types=1);

namespace App\External\Infrastructure\RoverNavigator\Output;

use App\Core\Application\RoverNavigator\Output\RoverNavigatorOutputInterface;
use App\Core\Application\RoverPosition\DTO\RoverPosition;
use App\Core\Domain\Rover\Entity\Rover;

final readonly class RoverNavigatorOutput implements RoverNavigatorOutputInterface
{
    public function output(Rover $rover): RoverPosition
    {
        $coordinate = $rover->getCoordinate();
        $orientation = $rover->getOrientation();

        $position = sprintf('%s %s %s', $coordinate->getX(), $coordinate->getY(), $orientation->getAbbreviation()->value);

        return new RoverPosition($position);
    }
}