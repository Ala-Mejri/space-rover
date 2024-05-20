<?php

declare(strict_types=1);

namespace App\Core\Domain\Orientation\Factory;

use App\Core\Domain\Orientation\Entity\OrientationInterface;
use App\Core\Domain\Orientation\Enum\OrientationType;
use App\Core\Domain\Orientation\Exception\InvalidOrientationException;

final class OrientationFactory
{
    /**
     * @throws InvalidOrientationException
     */
    public function create(string $orientationKey): OrientationInterface
    {
        $orientationType = OrientationType::tryFrom($orientationKey);

        if ($orientationType === null) {
            throw new InvalidOrientationException(sprintf('Provided orientation key %s is invalid', $orientationKey));
        }

        return $orientationType->getOrientation();
    }
}