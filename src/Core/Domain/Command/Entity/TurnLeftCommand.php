<?php

declare(strict_types=1);

namespace App\Core\Domain\Command\Entity;

use App\Core\Domain\Plateau\Entity\Plateau;
use App\Core\Domain\Rover\Entity\Rover;

final class TurnLeftCommand implements CommandInterface
{
    public function execute(Plateau $plateau, Rover $rover): void
    {
        $rover->moveLeft();
    }
}