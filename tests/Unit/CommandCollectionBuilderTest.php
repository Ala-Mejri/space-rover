<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Core\Application\Command\DTO\CommandCollection;
use App\Core\Application\Command\Service\CommandCollectionBuilder;
use App\Core\Domain\Command\Entity\MoveForwardCommand;
use App\Core\Domain\Command\Entity\TurnLeftCommand;
use App\Core\Domain\Command\Entity\TurnRightCommand;
use App\Core\Domain\Command\Exception\InvalidCommandKeyException;
use App\Core\Domain\Command\Factory\CommandFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers CommandCollectionBuilder
 */
class CommandCollectionBuilderTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $commandFactory;
    private CommandCollectionBuilder $commandCollectionBuilder;

    protected function setUp(): void
    {
        $this->commandFactory = $this->prophesize(CommandFactory::class);

        $this->commandCollectionBuilder = new CommandCollectionBuilder($this->commandFactory->reveal());
    }

    /**
     * @group Unit
     * @throws InvalidCommandKeyException
     * @dataProvider provideCorrectCommandCollectionData
     */
    public function testBuildShouldReturnCommandCollection(string $commandKeys, CommandCollection $expectedResult): void
    {
        // Arrange
        foreach (str_split($commandKeys) as $index => $commandKey) {
            $this->commandFactory->create($commandKey)
                ->shouldBeCalled()
                ->willReturn($expectedResult[$index]);
        }

        // Act
        $actualResult = $this->commandCollectionBuilder->build($commandKeys);

        // Assert
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @group Unit
     * @throws InvalidCommandKeyException
     * @dataProvider provideInvalidCommandData
     */
    public function testBuildShouldThrowExceptionWhenCommandIsInvalid(
        string $commandKeys,
        array  $validCommandKeys,
        string $invalidCommandKey,
        string $expectedExceptionMessage
    ): void
    {
        // Arrange
        $expectedException = new InvalidCommandKeyException($expectedExceptionMessage);

        foreach ($validCommandKeys as $validCommandKey) {
            $this->commandFactory->create($validCommandKey[0])
                ->shouldBeCalled()
                ->willReturn($validCommandKey[1]);
        }

        $this->commandFactory->create($invalidCommandKey)
            ->shouldBeCalledOnce()
            ->willThrow($expectedException);

        // Assert
        $this->expectExceptionObject($expectedException);

        // Act
        $this->commandCollectionBuilder->build($commandKeys);
    }

    private function provideCorrectCommandCollectionData(): array
    {
        return [
            'LMLMLMLMM' => [
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
            ],
            'MMRMMRMRRM' => [
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
            ],
        ];
    }

    private function provideInvalidCommandData(): array
    {
        return [
            'Should stop at A' => [
                'AMLMLMLMM',
                [],
                'A',
                'Provided command key A is invalid',
            ],
            'Should stop at B' => [
                'RLLBLLLM',
                [
                    ['R' , new TurnRightCommand()],
                    ['L' , new TurnLeftCommand()],
                    ['L' , new TurnLeftCommand()],
                ],
                'B',
                'Provided command key B is invalid',
            ],
            'Should stop at A and not B' => [
                'ABLLLLM',
                [],
                'A',
                'Provided command key A is invalid',
            ],
        ];
    }
}