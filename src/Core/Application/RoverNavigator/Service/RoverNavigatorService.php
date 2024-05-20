<?php

declare(strict_types=1);

namespace App\Core\Application\RoverNavigator\Service;

use App\Core\Application\Command\Service\CommandCollectionBuilder;
use App\Core\Application\RoverNavigator\Output\RoverNavigatorOutputInterface;
use App\Core\Application\RoverPosition\DTO\RoverPosition;
use App\Core\Domain\Command\Exception\InvalidCommandKeyException;
use App\Core\Domain\Plateau\Entity\Plateau;
use App\Core\Domain\Rover\Entity\Rover;

class RoverNavigatorService
{
    public function __construct(
        private readonly CommandCollectionBuilder      $commandCollectionBuilder,
        private readonly RoverNavigatorOutputInterface $roverNavigatorOutput,
    )
    {
    }

    /**
     * @throws InvalidCommandKeyException
     */
    public function navigate(Plateau $plateau, Rover $rover, string $commandKeys): RoverPosition
    {
        $this->executeCommands($plateau, $rover, $commandKeys);

        return $this->roverNavigatorOutput->output($rover);
    }

    /**
     * @throws InvalidCommandKeyException
     */
    private function executeCommands(Plateau $plateau, Rover $rover, string $commandKeys): void
    {
        $commandCollection = $this->commandCollectionBuilder->build($commandKeys);

        foreach ($commandCollection as $command) {
            $command->execute($plateau, $rover);
        }
    }
}