<?php

declare(strict_types=1);

namespace App\External\Presentation\RoverNavigator\Input;

use App\Shared\Domain\Exception\InputSourceNotFoundException;

interface RoverInputSourceReaderInterface
{
    /**
     * @throws InputSourceNotFoundException
     */
    public function getData(string $resourceName): string;
}