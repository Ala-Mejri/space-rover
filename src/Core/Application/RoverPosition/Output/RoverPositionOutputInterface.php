<?php

declare(strict_types=1);

namespace App\Core\Application\RoverPosition\Output;

use App\Core\Application\RoverPosition\DTO\RoverPositionCollection;

interface RoverPositionOutputInterface
{
    public function saveData(RoverPositionCollection $roverPositionCollection): void;
}