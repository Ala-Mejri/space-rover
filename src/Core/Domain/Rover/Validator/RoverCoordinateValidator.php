<?php

declare(strict_types=1);

namespace App\Core\Domain\Rover\Validator;

use App\Core\Domain\Plateau\Entity\Plateau;
use App\Core\Domain\Rover\Entity\Rover;
use App\Core\Domain\Rover\Exception\InvalidRoverCoordinateException;

class RoverCoordinateValidator
{
    /**
     * @throws InvalidRoverCoordinateException
     */
    public function validateRoverCoordinate(Plateau $plateau, Rover $rover): void
    {
        if ($this->doesRoverCoordinateExceedPlateauUpperRightBorder($plateau, $rover)) {
            throw new InvalidRoverCoordinateException('Provided rover coordinate has exceeded plateau upper right border');
        }

        if ($this->doesRoverCoordinateExceedPlateauBottomLeftBorder($plateau, $rover)) {
            throw new InvalidRoverCoordinateException('Provided rover coordinate has exceeded plateau bottom left border');
        }
    }

    private function doesRoverCoordinateExceedPlateauUpperRightBorder(Plateau $plateau, Rover $rover): bool
    {
        return $rover->getCoordinate()->getX() > $plateau->getUpperRightCoordinate()->getX()
            || $rover->getCoordinate()->getY() > $plateau->getUpperRightCoordinate()->getY();
    }

    private function doesRoverCoordinateExceedPlateauBottomLeftBorder(Plateau $plateau, Rover $rover): bool
    {
        return $rover->getCoordinate()->getX() < $plateau->getBottomLeftCoordinate()->getX()
            || $rover->getCoordinate()->getY() < $plateau->getBottomLeftCoordinate()->getY();
    }
}