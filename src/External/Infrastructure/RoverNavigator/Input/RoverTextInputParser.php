<?php

declare(strict_types=1);

namespace App\External\Infrastructure\RoverNavigator\Input;

use App\Core\Application\RoverNavigator\DTO\RoverNavigator;
use App\Core\Application\RoverNavigator\DTO\RoverNavigatorCollection;
use App\Core\Application\RoverNavigator\DTO\RoverNavigatorInput;
use App\External\Presentation\RoverNavigator\Input\RoverInputParserInterface;
use App\Shared\Domain\Exception\InvalidInputException;

final class RoverTextInputParser implements RoverInputParserInterface
{
    /**
     * @inheritDoc
     */
    public function parseData(string $string): RoverNavigatorInput
    {
        if ($string === '') {
            throw new InvalidInputException('Input file is empty');
        }

        $inputs = explode(PHP_EOL, $string);
        $plateauUpperRightCoordinate = $this->extractPlateauUpperRightCoordinate($inputs);

        $roverNavigatorCollection = $this->extractRoverNavigatorDTOs($inputs);

        return new RoverNavigatorInput(
            (int)$plateauUpperRightCoordinate[0],
            (int)$plateauUpperRightCoordinate[1],
            $roverNavigatorCollection,
        );
    }

    private function extractPlateauUpperRightCoordinate(array &$inputs): array
    {
        $firstInput = array_shift($inputs);

        return explode(' ', $firstInput);
    }

    /**
     * @throws InvalidInputException
     */
    private function extractRoverNavigatorDTOs(array $inputs): RoverNavigatorCollection
    {
        $roverNavigatorCollection = new RoverNavigatorCollection();

        foreach ($inputs as $index => $input) {
            if ($index % 2 !== 0) {
                continue;
            }

            $commandKeys = $this->extractCommandKeys($inputs, $index);
            $createRoverNavigatorDTO = $this->createRoverNavigatorDTO($input, $commandKeys);
            $roverNavigatorCollection->append($createRoverNavigatorDTO);
        }

        return $roverNavigatorCollection;
    }

    /**
     * @throws InvalidInputException
     */
    private function extractCommandKeys(array $inputs, int $index): string
    {
        $commandKeys = $inputs[$index + 1] ?? null;

        if ($commandKeys === '' || $commandKeys === null) {
            throw new InvalidInputException('No command keys was provided');
        }

        return $commandKeys;
    }

    private function createRoverNavigatorDTO(string $input, string $commandKeys): RoverNavigator
    {
        $roverCurrentPosition = explode(' ', $input);

        return new RoverNavigator(
            (int)$roverCurrentPosition[0],
            (int)$roverCurrentPosition[1],
            $roverCurrentPosition[2],
            $commandKeys,
        );
    }
}