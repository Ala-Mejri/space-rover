<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Service;

use App\Core\Application\Command\DTO\CommandCollection;
use App\Core\Domain\Command\Entity\CommandInterface;
use App\Core\Domain\Command\Exception\InvalidCommandKeyException;
use App\Core\Domain\Command\Factory\CommandFactory;

class CommandCollectionBuilder
{
    public function __construct(private readonly CommandFactory $commandFactory)
    {
    }

    /**
     * @return CommandInterface[]
     * @throws InvalidCommandKeyException
     */
    public function build(string $commandKeys): CommandCollection
    {
        $commandKeys = str_split($commandKeys);
        $commandCollection = new CommandCollection();

        foreach ($commandKeys as $commandKey) {
            $command = $this->commandFactory->create($commandKey);
            $commandCollection->append($command);
        }

        return $commandCollection;
    }
}