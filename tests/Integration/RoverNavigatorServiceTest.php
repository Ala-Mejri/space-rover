<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Core\Application\RoverNavigator\Service\RoverNavigatorService;
use App\Core\Application\RoverPosition\DTO\RoverPosition;
use App\Core\Domain\Command\Exception\InvalidCommandKeyException;
use App\Core\Domain\Orientation\Entity\EastOrientation;
use App\Core\Domain\Orientation\Entity\NorthOrientation;
use App\Core\Domain\Orientation\Entity\SouthOrientation;
use App\Core\Domain\Plateau\Entity\Plateau;
use App\Core\Domain\Rover\Entity\Rover;
use App\Shared\Domain\Coordinate\ValueObject\Coordinate;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers RoverNavigatorService
 */
class RoverNavigatorServiceTest extends KernelTestCase
{
    /**
     * @group Integration
     * @throws InvalidCommandKeyException
     * @dataProvider provideCorrectNavigationData
     */
    public function testNavigateShouldReturnCorrectOutput(
        Plateau       $plateau,
        Rover         $rover,
        string        $commandKeys,
        RoverPosition $expectedResult,
    ): void
    {
        // Arrange
        self::bootKernel();

        $roverNavigatorService = $this->getContainer()->get(RoverNavigatorService::class);
        assert($roverNavigatorService instanceof RoverNavigatorService);

        // Act
        $actualResult = $roverNavigatorService->navigate($plateau, $rover, $commandKeys);

        // Assert
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @group Integration
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
        self::bootKernel();

        $roverNavigatorService = $this->getContainer()->get(RoverNavigatorService::class);
        assert($roverNavigatorService instanceof RoverNavigatorService);

        $expectedException = new InvalidCommandKeyException($expectedExceptionMessage);

        // Assert
        $this->expectExceptionObject($expectedException);

        // Act
        $roverNavigatorService->navigate($plateau, $rover, $commandKeys);
    }

    private function provideCorrectNavigationData(): array
    {
        return [
            '1 3 N' => [
                new Plateau(5, 5),
                new Rover(new Coordinate(1, 2), new NorthOrientation()),
                'LMLMLMLMM',
                new RoverPosition('1 3 N'),
            ],
            '5 1 E' => [
                new Plateau(5, 5),
                new Rover(new Coordinate(3, 3), new EastOrientation()),
                'MMRMMRMRRM',
                new RoverPosition('5 1 E'),
            ],
            '3 5 N (Should not exceed upper right border)' => [
                new Plateau(5, 5),
                new Rover(new Coordinate(3, 5), new NorthOrientation()),
                'RMMMMMLMM',
                new RoverPosition('5 5 N'),
            ],
            '1 3 S (Should not exceed bottom left border)' => [
                new Plateau(5, 5),
                new Rover(new Coordinate(3, 3), new SouthOrientation()),
                'RMMMMMLML',
                new RoverPosition('0 2 E'),
            ],
        ];
    }

    private function provideInvalidRoverCoordinateData(): array
    {
        return [
            'Upper right border exceeded' => [
                new Plateau(5, 5),
                new Rover(new Coordinate(6, 7), new NorthOrientation()),
                'LMLMLMLMM',
                'Provided rover coordinate has exceeded plateau upper right border',
            ],
            'Bottom left border exceeded' => [
                new Plateau(5, 5),
                new Rover(new Coordinate(-1, -4), new NorthOrientation()),
                'MMRMMRMRRM',
                'Provided rover coordinate has exceeded plateau bottom left border',
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