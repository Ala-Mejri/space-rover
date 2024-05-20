<?php

declare(strict_types=1);

namespace App\Core\Domain\Plateau\Entity;

use App\Shared\Domain\Coordinate\ValueObject\Coordinate;

/*
 * Todo: Maybe we can make the Plateau an Aggregate that contains multiple Rovers
 * And be responsible for setting its own rovers and making sure they are valid (not exceeding the plateau borders)
 * */
final class Plateau
{
    private Coordinate $bottomLeftCoordinate;
    private Coordinate $upperRightCoordinate;

    public function __construct(int $x, int $y)
    {
        $this->bottomLeftCoordinate = new Coordinate(0, 0);
        $this->upperRightCoordinate = new Coordinate($x, $y);
    }

    public function getBottomLeftCoordinate(): Coordinate
    {
        return $this->bottomLeftCoordinate;
    }

    public function getUpperRightCoordinate(): Coordinate
    {
        return $this->upperRightCoordinate;
    }
}