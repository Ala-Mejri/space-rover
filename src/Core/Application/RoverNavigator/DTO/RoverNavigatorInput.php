<?php

declare(strict_types=1);

namespace App\Core\Application\RoverNavigator\DTO;

final readonly class RoverNavigatorInput
{
    /**
     * @var RoverNavigator[] $roverNavigatorCollection
     */
    public function __construct(
        public int                      $plateauUpperRightCoordinateX,
        public int                      $plateauUpperRightCoordinateY,
        public RoverNavigatorCollection $roverNavigatorCollection,
    )
    {
    }
}