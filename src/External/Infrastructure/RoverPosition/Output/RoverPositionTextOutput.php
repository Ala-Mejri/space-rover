<?php

declare(strict_types=1);

namespace App\External\Infrastructure\RoverPosition\Output;

use App\Core\Application\RoverPosition\DTO\RoverPositionCollection;
use App\Core\Application\RoverPosition\Output\RoverPositionOutputInterface;
use App\Shared\Application\Service\ContainerParametersServiceInterface;

final readonly class RoverPositionTextOutput implements RoverPositionOutputInterface
{
    public function __construct(private string $outputFileName, private ContainerParametersServiceInterface $containerParametersService)
    {
    }

    public function saveData(RoverPositionCollection $roverPositionCollection): void
    {
        $fileName = $this->containerParametersService->getRootDir() . DIRECTORY_SEPARATOR . $this->outputFileName;

        $data = implode(PHP_EOL, $roverPositionCollection->getArrayCopy());

        file_put_contents($fileName, $data);
    }
}