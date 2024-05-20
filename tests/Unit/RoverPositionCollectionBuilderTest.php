<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Core\Application\RoverNavigator\DTO\RoverNavigator;
use App\Core\Application\RoverNavigator\DTO\RoverNavigatorCollection;
use App\Core\Application\RoverNavigator\DTO\RoverNavigatorInput;
use App\Core\Application\RoverNavigator\Service\RoverNavigatorService;
use App\Core\Application\RoverPosition\DTO\RoverPosition;
use App\Core\Application\RoverPosition\DTO\RoverPositionCollection;
use App\Core\Application\RoverPosition\Service\RoverPositionCollectionBuilder;
use App\Core\Domain\Command\Exception\InvalidCommandKeyException;
use App\Core\Domain\Orientation\Entity\EastOrientation;
use App\Core\Domain\Orientation\Entity\NorthOrientation;
use App\Core\Domain\Orientation\Exception\InvalidOrientationException;
use App\Core\Domain\Plateau\Entity\Plateau;
use App\Core\Domain\Plateau\Factory\PlateauFactory;
use App\Core\Domain\Rover\Entity\Rover;
use App\Core\Domain\Rover\Exception\InvalidRoverCoordinateException;
use App\Core\Domain\Rover\Factory\RoverFactory;
use App\Shared\Domain\Coordinate\ValueObject\Coordinate;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers RoverPositionCollectionBuilder
 */
class RoverPositionCollectionBuilderTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $roverFactory;
    private ObjectProphecy $plateauFactory;
    private ObjectProphecy $roverNavigatorService;
    private RoverPositionCollectionBuilder $roverPositionCollectionService;

    protected function setUp(): void
    {
        $this->plateauFactory = $this->prophesize(PlateauFactory::class);
        $this->roverFactory = $this->prophesize(RoverFactory::class);
        $this->roverNavigatorService = $this->prophesize(RoverNavigatorService::class);

        $this->roverPositionCollectionService = new RoverPositionCollectionBuilder(
            $this->plateauFactory->reveal(),
            $this->roverFactory->reveal(),
            $this->roverNavigatorService->reveal(),
        );
    }

    /**
     * @group Unit
     * @throws InvalidOrientationException
     * @throws InvalidRoverCoordinateException
     * @throws InvalidCommandKeyException
     * @dataProvider provideCorrectNavigationData
     */
    public function testGetPositionsShouldReturnRoverPositionCollection(
        Plateau                 $plateau,
        array                   $rovers,
        RoverNavigatorInput     $roverNavigatorInput,
        RoverPositionCollection $expectedResult,
    ): void
    {
        // Arrange
        $this->plateauFactory->create(
            $roverNavigatorInput->plateauUpperRightCoordinateX,
            $roverNavigatorInput->plateauUpperRightCoordinateY,
        )->shouldBeCalledOnce()
            ->willReturn($plateau);

        foreach ($roverNavigatorInput->roverNavigatorCollection as $index => $roverNavigator) {
            $rover = $rovers[$index];
            $this->roverFactory->create(
                $plateau,
                $roverNavigator->roverCurrentCoordinateX,
                $roverNavigator->roverCurrentCoordinateY,
                $roverNavigator->orientationKey,
            )->shouldBeCalledOnce()
                ->willReturn($rover);

            $roverPosition = new RoverPosition($expectedResult[$index]);

            $this->roverNavigatorService->navigate($plateau, $rover, $roverNavigator->commandKeys)
                ->shouldBeCalledOnce()
                ->willReturn($roverPosition);
        }

        // Act
        $actualResult = $this->roverPositionCollectionService->build($roverNavigatorInput);

        // Assert
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @group Unit
     * @throws InvalidOrientationException
     * @throws InvalidRoverCoordinateException
     * @throws InvalidCommandKeyException
     * @dataProvider provideInvalidOrientationData
     */
    public function testGetPositionsShouldThrowExceptionWhenOrientationIsInvalid(
        Plateau             $plateau,
        RoverNavigatorInput $roverNavigatorInput,
        string              $expectedExceptionMessage,
    ): void
    {
        // Arrange
        $expectedException = new InvalidOrientationException($expectedExceptionMessage);

        $this->plateauFactory->create(
            $roverNavigatorInput->plateauUpperRightCoordinateX,
            $roverNavigatorInput->plateauUpperRightCoordinateY,
        )->shouldBeCalledOnce()
            ->willReturn($plateau);

        foreach ($roverNavigatorInput->roverNavigatorCollection as $roverNavigator) {
            $this->roverFactory->create(
                $plateau,
                $roverNavigator->roverCurrentCoordinateX,
                $roverNavigator->roverCurrentCoordinateY,
                $roverNavigator->orientationKey,
            )->shouldBeCalledOnce()
                ->willThrow($expectedException);

            $this->roverNavigatorService->navigate()->shouldNotBeCalled();
        }

        // Assert
        $this->expectExceptionObject($expectedException);

        // Act
        $this->roverPositionCollectionService->build($roverNavigatorInput);
    }

    /**
     * @group Unit
     * @throws InvalidOrientationException
     * @throws InvalidRoverCoordinateException
     * @throws InvalidCommandKeyException
     * @dataProvider provideInvalidRoverCoordinateData
     */
    public function testGetPositionsShouldThrowExceptionWhenRoverCoordinateIsInvalid(
        Plateau             $plateau,
        RoverNavigatorInput $roverNavigatorInput,
        string              $expectedExceptionMessage,
    ): void
    {
        // Arrange
        $expectedException = new InvalidRoverCoordinateException($expectedExceptionMessage);

        $this->plateauFactory->create(
            $roverNavigatorInput->plateauUpperRightCoordinateX,
            $roverNavigatorInput->plateauUpperRightCoordinateY,
        )->shouldBeCalledOnce()
            ->willReturn($plateau);

        foreach ($roverNavigatorInput->roverNavigatorCollection as $roverNavigator) {
            $this->roverFactory->create(
                $plateau,
                $roverNavigator->roverCurrentCoordinateX,
                $roverNavigator->roverCurrentCoordinateY,
                $roverNavigator->orientationKey,
            )->shouldBeCalledOnce()
                ->willThrow($expectedException);
        }

        // Assert
        $this->expectExceptionObject($expectedException);

        // Act
        $this->roverPositionCollectionService->build($roverNavigatorInput);
    }

    /**
     * @group Unit
     * @throws InvalidOrientationException
     * @throws InvalidRoverCoordinateException
     * @throws InvalidCommandKeyException
     * @dataProvider provideInvalidCommandData
     */
    public function testGetPositionsShouldThrowExceptionWhenCommandIsInvalid(
        Plateau             $plateau,
        array               $rovers,
        RoverNavigatorInput $roverNavigatorInput,
        string              $expectedExceptionMessage,
    ): void
    {
        // Arrange
        $expectedException = new InvalidCommandKeyException($expectedExceptionMessage);

        $this->plateauFactory->create(
            $roverNavigatorInput->plateauUpperRightCoordinateX,
            $roverNavigatorInput->plateauUpperRightCoordinateY,
        )->shouldBeCalledOnce()
            ->willReturn($plateau);

        foreach ($roverNavigatorInput->roverNavigatorCollection as $index => $roverNavigator) {
            $rover = $rovers[$index];
            $this->roverFactory->create(
                $plateau,
                $roverNavigator->roverCurrentCoordinateX,
                $roverNavigator->roverCurrentCoordinateY,
                $roverNavigator->orientationKey,
            )->shouldBeCalledOnce()
                ->willReturn($rover);

            $this->roverNavigatorService->navigate($plateau, $rover, $roverNavigator->commandKeys)
                ->shouldBeCalledOnce()
                ->willThrow($expectedException);
        }

        // Assert
        $this->expectExceptionObject($expectedException);

        // Act
        $this->roverPositionCollectionService->build($roverNavigatorInput);
    }

    private function provideCorrectNavigationData(): array
    {
        return [
            '1 3 N - 5 1 E' => [
                new Plateau(5, 5),
                [
                    new Rover(
                        new Coordinate(1, 2),
                        new NorthOrientation(),
                    ),
                    new Rover(
                        new Coordinate(3, 3),
                        new EastOrientation(),
                    ),
                ],
                new RoverNavigatorInput(
                    5,
                    5,
                    new RoverNavigatorCollection([
                        new RoverNavigator(
                            1,
                            2,
                            'N',
                            'LMLMLMLMM',
                        ),
                        new RoverNavigator(
                            3,
                            3,
                            'E',
                            'MMRMMRMRRM',
                        ),
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
                new Plateau(5, 5),
                new RoverNavigatorInput(
                    5,
                    5,
                    new RoverNavigatorCollection([
                        new RoverNavigator(
                            1,
                            2,
                            'A',
                            'LMLMLMLMM',
                        ),
                    ]),
                ),
                'Provided orientation key A is invalid',
            ],
        ];
    }

    private function provideInvalidRoverCoordinateData(): array
    {
        return [
            'Invalid upper right coordinate [x => 6, y => 7]' => [
                new Plateau(5, 5),
                new RoverNavigatorInput(
                    5,
                    5,
                    new RoverNavigatorCollection([
                        new RoverNavigator(
                            7,
                            6,
                            'N',
                            'LMLMLMLMM',
                        ),
                    ]),
                ),
                'Provided rover coordinate has exceeded plateau upper right border',
            ],
            'Invalid bottom left coordinate [x => -1, y => -4]' => [
                new Plateau(5, 5),
                new RoverNavigatorInput(
                    5,
                    5,
                    new RoverNavigatorCollection([
                        new RoverNavigator(
                            -1,
                            -4,
                            'E',
                            'LMLMLMLMM',
                        ),
                    ]),
                ),
                'Provided rover coordinate has exceeded plateau bottom left border',
            ],
        ];
    }

    private function provideInvalidCommandData(): array
    {
        return [
            'Invalid command key [C]' => [
                new Plateau(5, 5),
                [
                    new Rover(
                        new Coordinate(5, 5),
                        new NorthOrientation(),
                    ),
                ],
                new RoverNavigatorInput(
                    5,
                    5,
                    new RoverNavigatorCollection([
                        new RoverNavigator(
                            5,
                            5,
                            'N',
                            'CBLLLLML',
                        ),
                    ]),
                ),
                'Provided command key C is invalid',
            ],
        ];
    }
}