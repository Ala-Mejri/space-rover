<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Core\Application\Command\DTO\CommandCollection;
use App\Core\Application\Command\Service\CommandCollectionBuilder;
use App\Core\Application\RoverNavigator\Output\RoverNavigatorOutputInterface;
use App\Core\Application\RoverNavigator\Service\RoverNavigatorService;
use App\Core\Application\RoverPosition\DTO\RoverPosition;
use App\Core\Domain\Command\Entity\MoveForwardCommand;
use App\Core\Domain\Command\Entity\TurnLeftCommand;
use App\Core\Domain\Command\Entity\TurnRightCommand;
use App\Core\Domain\Command\Exception\InvalidCommandKeyException;
use App\Core\Domain\Orientation\Entity\EastOrientation;
use App\Core\Domain\Orientation\Entity\NorthOrientation;
use App\Core\Domain\Plateau\Entity\Plateau;
use App\Core\Domain\Rover\Entity\Rover;
use App\Shared\Domain\Coordinate\ValueObject\Coordinate;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers RoverNavigatorService
 */
class RoverNavigatorServiceTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $roverNavigatorOutput;
    private ObjectProphecy $commandCollectionBuilder;
    private RoverNavigatorService $roverNavigatorService;

    protected function setUp(): void
    {
        $this->roverNavigatorOutput = $this->prophesize(RoverNavigatorOutputInterface::class);
        $this->commandCollectionBuilder = $this->prophesize(CommandCollectionBuilder::class);

        $this->roverNavigatorService = new RoverNavigatorService(
            $this->commandCollectionBuilder->reveal(),
            $this->roverNavigatorOutput->reveal(),
        );
    }

    /**
     * @group Unit
     * @throws InvalidCommandKeyException
     * @dataProvider provideCorrectNavigationData
     */
    public function testNavigateShouldReturnCorrectOutput(
        Plateau           $plateau,
        Rover             $rover,
        string            $commandKeys,
        CommandCollection $commandCollection,
        RoverPosition     $expectedResult,
    ): void
    {
        // Arrange
        $this->commandCollectionBuilder->build($commandKeys)
            ->shouldBeCalledOnce()
            ->willReturn($commandCollection);

        $this->roverNavigatorOutput->output($rover)
            ->shouldBeCalledOnce()
            ->willReturn($expectedResult);

        // Act
        $actualResult = $this->roverNavigatorService->navigate($plateau, $rover, $commandKeys);

        // Assert
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @group Unit
     * @throws InvalidCommandKeyException
     * @dataProvider provideInvalidCommandData
     */
    public function testNavigateShouldThrowExceptionWhenCommandIsInvalid(
        Plateau $plateau,
        Rover   $rover,
        string  $commandKeys,
        string  $expectedExceptionMessage,
    ): void
    {
        // Arrange
        $expectedException = new InvalidCommandKeyException($expectedExceptionMessage);

        $this->commandCollectionBuilder->build($commandKeys)
            ->shouldBeCalledOnce()
            ->willThrow($expectedException);

        $this->roverNavigatorOutput->output($rover)
            ->shouldNotBeCalled();

        // Assert
        $this->expectExceptionObject($expectedException);

        // Act
        $this->roverNavigatorService->navigate($plateau, $rover, $commandKeys);
    }

    private function provideCorrectNavigationData(): array
    {
        return [
            '1 3 N' => [
                new Plateau(5, 5),
                new Rover(new Coordinate(1, 2), new NorthOrientation()),
                'LMLMLMLMM',
                new CommandCollection([
                    new TurnLeftCommand(),
                    new MoveForwardCommand(),
                    new TurnLeftCommand(),
                    new MoveForwardCommand(),
                    new TurnLeftCommand(),
                    new MoveForwardCommand(),
                    new TurnLeftCommand(),
                    new MoveForwardCommand(),
                    new MoveForwardCommand(),
                ]),
                new RoverPosition('1 3 N'),
            ],
            '5 1 E' => [
                new Plateau(5, 5),
                new Rover(new Coordinate(3, 3), new EastOrientation()),
                'MMRMMRMRRM',
                new CommandCollection([
                    new MoveForwardCommand(),
                    new MoveForwardCommand(),
                    new TurnRightCommand(),
                    new MoveForwardCommand(),
                    new MoveForwardCommand(),
                    new TurnRightCommand(),
                    new MoveForwardCommand(),
                    new TurnRightCommand(),
                    new TurnRightCommand(),
                    new MoveForwardCommand(),
                ]),
                new RoverPosition('5 1 E'),
            ],
        ];
    }

    private function provideInvalidCommandData(): array
    {
        return [
            'A' => [
                new Plateau(5, 5),
                new Rover(new Coordinate(1, 2), new NorthOrientation()),
                'ALMLMLMLMM',
                sprintf('Provided command key %s is invalid', 'A'),
            ],
            'B' => [
                new Plateau(5, 5),
                new Rover(new Coordinate(3, 3), new EastOrientation()),
                'LLBLLLM',
                sprintf('Provided command key %s is invalid', 'B'),
            ],
        ];
    }
}