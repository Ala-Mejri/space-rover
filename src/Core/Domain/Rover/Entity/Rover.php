<?php

declare(strict_types=1);

namespace App\Core\Domain\Rover\Entity;

use App\Core\Domain\Orientation\Entity\OrientationInterface;
use App\Core\Domain\Plateau\Entity\Plateau;
use App\Shared\Domain\Coordinate\ValueObject\Coordinate;

final class Rover
{
    public function __construct(private Coordinate $coordinate, private OrientationInterface $orientation)
    {
    }

    public function getOrientation(): OrientationInterface
    {
        return $this->orientation;
    }

    public function setOrientation(OrientationInterface $orientation): void
    {
        $this->orientation = $orientation;
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function setCoordinate(Coordinate $coordinate): void
    {
        $this->coordinate = $coordinate;
    }

    public function turnLeft(): void
    {
        $this->setOrientation($this->getOrientation()->left());
    }

    public function turnRight(): void
    {
        $this->setOrientation($this->getOrientation()->right());
    }

    public function moveForward(Plateau $plateau): void
    {
        $newCoordinate = $this->getOrientation()->moveForward();

        $x = $this->getCoordinate()->getX() + $newCoordinate->getX();
        $x = $this->normalizeXCoordinate($plateau, $x);

        $y = $this->getCoordinate()->getY() + $newCoordinate->getY();
        $y = $this->normalizeYCoordinate($plateau, $y);

        $this->setCoordinate(new Coordinate($x, $y));
    }

    private function normalizeXCoordinate(Plateau $plateau, int $x): int
    {
        if ($x > $plateau->getUpperRightCoordinate()->getX()) {
            return $plateau->getUpperRightCoordinate()->getX();
        }

        if ($x < $plateau->getBottomLeftCoordinate()->getX()) {
            return $plateau->getBottomLeftCoordinate()->getX();
        }

        return $x;
    }

    private function normalizeYCoordinate(Plateau $plateau, int $y): int
    {
        if ($y > $plateau->getUpperRightCoordinate()->getY()) {
            $y = $plateau->getUpperRightCoordinate()->getY();
        }

        if ($y < $plateau->getBottomLeftCoordinate()->getY()) {
            $y = $plateau->getBottomLeftCoordinate()->getY();
        }

        return $y;
    }
}