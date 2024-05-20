<?php

declare(strict_types=1);

namespace App\Core\Application\RoverNavigator\Output;

use App\Core\Application\RoverPosition\DTO\RoverPosition;
use App\Core\Domain\Rover\Entity\Rover;

interface RoverNavigatorOutputInterface
{
    public function output(Rover $rover): RoverPosition;
}