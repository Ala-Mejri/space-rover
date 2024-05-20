<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Core\Application\RoverNavigator\DTO\RoverNavigator;
use App\Core\Application\RoverNavigator\DTO\RoverNavigatorCollection;
use App\Core\Application\RoverNavigator\DTO\RoverNavigatorInput;
use App\Core\Application\RoverPosition\DTO\RoverPositionCollection;
use App\Core\Application\RoverPosition\Service\RoverPositionCollectionBuilder;
use App\Core\Domain\Command\Exception\InvalidCommandKeyException;
use App\Core\Domain\Orientation\Exception\InvalidOrientationException;
use App\Core\Domain\Rover\Exception\InvalidRoverCoordinateException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers RoverPositionCollectionBuilder
 */
class RoverPositionCollectionBuilderTest extends KernelTestCase
{
    /**
     * @group Integration
     * @throws InvalidOrientationException
     * @throws InvalidRoverCoordinateException
     * @throws InvalidCommandKeyException
     * @dataProvider provideCorrectNavigationData
     */
    public function testGetPositionsShouldReturnRoverPositionCollection(
        RoverNavigatorInput     $roverNavigatorInput,
        RoverPositionCollection $expectedResult,
    ): void
    {
        // Arrange
        self::bootKernel();

        $roverPositionCollectionService = $this->getContainer()->get(RoverPositionCollectionBuilder::class);
        assert($roverPositionCollectionService instanceof RoverPositionCollectionBuilder);

        // Act
        $actualResult = $roverPositionCollectionService->build($roverNavigatorInput);

        // Assert
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @group Integration
     * @throws InvalidOrientationException
     * @throws InvalidRoverCoordinateException
     * @throws InvalidCommandKeyException
     * @dataProvider provideInvalidOrientationData
     */
    public function testGetPositionsShouldThrowExceptionWhenOrientationIsInvalid(
        RoverNavigatorInput $roverNavigatorInput,
        string              $expectedExceptionMessage,
    ): void
    {
        // Arrange
        self::bootKernel();

        $roverPositionCollectionService = $this->getContainer()->get(RoverPositionCollectionBuilder::class);
        assert($roverPositionCollectionService instanceof RoverPositionCollectionBuilder);

        $expectedException = new InvalidOrientationException($expectedExceptionMessage);

        // Assert
        $this->expectExceptionObject($expectedException);

        // Act
        $roverPositionCollectionService->build($roverNavigatorInput);
    }

    /**
     * @group Integration
     * @throws InvalidOrientationException
     * @throws InvalidRoverCoordinateException
     * @throws InvalidCommandKeyException
     * @dataProvider provideInvalidRoverCoordinateData
     */
    public function testGetPositionsShouldThrowExceptionWhenRoverCoordinateIsInvalid(
        RoverNavigatorInput $roverNavigatorInput,
        string              $expectedExceptionMessage,
    ): void
    {
        // Arrange
        self::bootKernel();

        $roverPositionCollectionService = $this->getContainer()->get(RoverPositionCollectionBuilder::class);
        assert($roverPositionCollectionService instanceof RoverPositionCollectionBuilder);

        $expectedException = new InvalidRoverCoordinateException($expectedExceptionMessage);

        // Assert
        $this->expectExceptionObject($expectedException);

        // Act
        $roverPositionCollectionService->build($roverNavigatorInput);
    }

    /**
     * @group Integration
     * @throws InvalidOrientationException
     * @throws InvalidRoverCoordinateException
     * @throws InvalidCommandKeyException
     * @dataProvider provideInvalidCommandData
     */
    public function testGetPositionsShouldThrowExceptionWhenCommandIsInvalid(
        RoverNavigatorInput $roverNavigatorInput,
        string              $expectedExceptionMessage,
    ): void
    {
        // Arrange
        self::bootKernel();

        $roverPositionCollectionService = $this->getContainer()->get(RoverPositionCollectionBuilder::class);
        assert($roverPositionCollectionService instanceof RoverPositionCollectionBuilder);

        $expectedException = new InvalidCommandKeyException($expectedExceptionMessage);

        // Assert
        $this->expectExceptionObject($expectedException);

        // Act
        $roverPositionCollectionService->build($roverNavigatorInput);
    }

    private function provideCorrectNavigationData(): array
    {
        return [
            '1 3 N - 5 1 E' => [
                new RoverNavigatorInput(
                    5,
                    5,
                    new RoverNavigatorCollection([
                        new RoverNavigator(1, 2, 'N', 'LMLMLMLMM'),
                        new RoverNavigator(3, 3, 'E', 'MMRMMRMRRM'),
                    ]),
                ),
                new RoverPositionCollection(['1 3 N', '5 1 E']),
            ],
        ];
    }

    private function provideInvalidOrientationData(): array
    {
        return [
            'Invalid orientation [A]' => [
                new RoverNavigatorInput(
                    5,
                    5,
                    new RoverNavigatorCollection([
                        new RoverNavigator(1, 2, 'A', 'LMLMLMLMM'),
                        new RoverNavigator(3, 3, 'E', 'MMRMMRMRRM'),
                    ]),
                ),
                'Provided orientation key A is invalid',
            ],
        ];
    }

    private function provideInvalidRoverCoordinateData(): array
    {
        return [
            'Invalid Coordinate [x => 6, y => 7]' => [
                new RoverNavigatorInput(
                    5,
                    5,
                    new RoverNavigatorCollection([
                        new RoverNavigator(7, 6, 'N', 'LMLMLMLMM'),
                        new RoverNavigator(3, 3, 'E', 'MMRMMRMRRM'),
                    ]),
                ),
                'Provided rover coordinate has exceeded plateau upper right border',
            ],
        ];
    }

    private function provideInvalidCommandData(): array
    {
        return [
            'Invalid command key [C]' => [
                new RoverNavigatorInput(
                    5,
                    5,
                    new RoverNavigatorCollection([
                        new RoverNavigator(5, 5, 'N', 'CBLLLLML'),
                        new RoverNavigator(3, 3, 'E', 'MMRMMRMRRM'),
                    ]),
                ),
                'Provided command key C is invalid',
            ],
            'Invalid command key [P]' => [
                new RoverNavigatorInput(
                    5,
                    5,
                    new RoverNavigatorCollection([
                        new RoverNavigator(1, 2, 'N', 'LMLMLMLMM'),
                        new RoverNavigator(3, 3, 'E', 'LLLPLLML'),
                    ]),
                ),
                'Provided command key P is invalid',
            ],
        ];
    }
}