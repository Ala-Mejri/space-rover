<?php

declare(strict_types=1);

namespace App\Core\Domain\Plateau\Factory;

use App\Core\Domain\Plateau\Entity\Plateau;

class PlateauFactory
{
    public function create(int $x, int $y): Plateau
    {
        return new Plateau($x, $y);
    }
}