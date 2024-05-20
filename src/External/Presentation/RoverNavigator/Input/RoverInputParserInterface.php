<?php

declare(strict_types=1);

namespace App\External\Presentation\RoverNavigator\Input;

use App\Core\Application\RoverNavigator\DTO\RoverNavigatorInput;
use App\Shared\Presentation\Exception\InvalidInputException;

interface RoverInputParserInterface
{
    /**
     * @throws InvalidInputException
     */
    public function parseData(string $string): RoverNavigatorInput;
}