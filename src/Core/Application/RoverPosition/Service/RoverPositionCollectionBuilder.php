<?php

declare(strict_types=1);

namespace App\Core\Application\RoverPosition\Service;

use App\Core\Application\RoverNavigator\DTO\RoverNavigatorInput;
use App\Core\Application\RoverNavigator\Service\RoverNavigatorService;
use App\Core\Application\RoverPosition\DTO\RoverPositionCollection;
use App\Core\Domain\Command\Exception\InvalidCommandKeyException;
use App\Core\Domain\Orientation\Exception\InvalidOrientationException;
use App\Core\Domain\Plateau\Factory\PlateauFactory;
use App\Core\Domain\Rover\Exception\InvalidRoverCoordinateException;
use App\Core\Domain\Rover\Factory\RoverFactory;

final readonly class RoverPositionCollectionBuilder
{
    public function __construct(
        private PlateauFactory        $plateauFactory,
        private RoverFactory          $roverFactory,
        private RoverNavigatorService $roverNavigatorService,
    )
    {
    }

    /**
     * @throws InvalidOrientationException
     * @throws InvalidRoverCoordinateException
     * @throws InvalidCommandKeyException
     */
    public function build(RoverNavigatorInput $roverNavigatorInput): RoverPositionCollection
    {
        $plateau = $this->plateauFactory->create(
            $roverNavigatorInput->plateauUpperRightCoordinateX,
            $roverNavigatorInput->plateauUpperRightCoordinateY,
        );

        $roverPositionCollection = new RoverPositionCollection();

        foreach ($roverNavigatorInput->roverNavigatorCollection as $roverNavigator) {
            $rover = $this->roverFactory->create(
                $plateau,
                $roverNavigator->roverCurrentCoordinateX,
                $roverNavigator->roverCurrentCoordinateY,
                $roverNavigator->orientationKey,
            );

            $roverPosition = $this->roverNavigatorService->navigate($plateau, $rover, $roverNavigator->commandKeys);
            $roverPositionCollection->append($roverPosition->value);
        }

        return $roverPositionCollection;
    }
}