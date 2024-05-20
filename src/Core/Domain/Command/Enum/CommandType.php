<?php

declare(strict_types=1);

namespace App\Core\Domain\Command\Enum;

use App\Core\Domain\Command\Entity\CommandInterface;
use App\Core\Domain\Command\Entity\MoveForwardCommand;
use App\Core\Domain\Command\Entity\TurnLeftCommand;
use App\Core\Domain\Command\Entity\TurnRightCommand;

enum CommandType: string
{
    case TURN_LEFT = 'L';
    case TURN_RIGHT = 'R';
    case MOVE_FORWARD = 'M';

    public function getCommand(): CommandInterface
    {
        return match ($this) {
            self::TURN_LEFT => new TurnLeftCommand(),
            self::TURN_RIGHT => new TurnRightCommand(),
            self::MOVE_FORWARD => new MoveForwardCommand(),
        };
    }
}
