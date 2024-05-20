<?php

declare(strict_types=1);

namespace App\Core\Domain\Command\Factory;

use App\Core\Domain\Command\Entity\CommandInterface;
use App\Core\Domain\Command\Enum\CommandType;
use App\Core\Domain\Command\Exception\InvalidCommandKeyException;

class CommandFactory
{
    /**
     * @throws InvalidCommandKeyException
     */
    public function create(string $commandKey): CommandInterface
    {
        $commandType = CommandType::tryFrom($commandKey);

        if ($commandType === null) {
            throw new InvalidCommandKeyException(sprintf('Provided command key %s is invalid', $commandKey));
        }

        return $commandType->getCommand();
    }
}