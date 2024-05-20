<?php

declare(strict_types=1);

namespace App\External\Infrastructure\RoverNavigator\Input;

use App\External\Presentation\RoverNavigator\Input\RoverInputSourceReaderInterface;
use App\Shared\Application\Service\ContainerParametersServiceInterface;
use App\Shared\Domain\Exception\InputSourceNotFoundException;

final readonly class RoverTextInputSourceReader implements RoverInputSourceReaderInterface
{
    public function __construct(private string $inputFileDir, private ContainerParametersServiceInterface $containerParametersService)
    {
    }

    /**
     * @inheritDoc
     */
    public function getData(string $resourceName): string
    {
        $fileName = $this->containerParametersService->getRootDir()
            . DIRECTORY_SEPARATOR
            . $this->inputFileDir
            . DIRECTORY_SEPARATOR
            . $resourceName;

        if (!file_exists($fileName)) {
            throw new InputSourceNotFoundException(sprintf('The requested input file "%s" was not found', $resourceName));
        }

        return file_get_contents($fileName);
    }
}