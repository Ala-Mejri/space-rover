<?php

declare(strict_types=1);

namespace App\Core\Domain\Rover\Factory;

use App\Core\Domain\Orientation\Exception\InvalidOrientationException;
use App\Core\Domain\Orientation\Factory\OrientationFactory;
use App\Core\Domain\Plateau\Entity\Plateau;
use App\Core\Domain\Rover\Entity\Rover;
use App\Core\Domain\Rover\Exception\InvalidRoverCoordinateException;
use App\Core\Domain\Rover\Validator\RoverCoordinateValidator;
use App\Shared\Domain\Coordinate\ValueObject\Coordinate;

class RoverFactory
{
    public function __construct(
        private readonly OrientationFactory       $orientationFactory,
        private readonly RoverCoordinateValidator $roverCoordinateValidator,
    )
    {
    }

    /**
     * @throws InvalidOrientationException
     * @throws InvalidRoverCoordinateException
     */
    public function create(Plateau $plateau, int $x, int $y, string $orientationKey): Rover
    {
        $coordinate = new Coordinate($x, $y);
        $orientation = $this->orientationFactory->create($orientationKey);
        $rover = new Rover($coordinate, $orientation);

        $this->roverCoordinateValidator->validateRoverCoordinate($plateau, $rover);

        return $rover;
    }
}