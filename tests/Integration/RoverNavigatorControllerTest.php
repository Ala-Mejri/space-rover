<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Core\Application\RoverPosition\DTO\RoverPosition;
use App\External\Presentation\RoverNavigator\Controller\RoverNavigatorController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers RoverNavigatorController
 */
class RoverNavigatorControllerTest extends KernelTestCase
{
    /**
     * @group Integration
     * @dataProvider provideCorrectRoverPositionData
     */
    public function testNavigateShouldReturnCorrectRoverPosition(string $resourceName, array $expectedResult): void
    {
        // Arrange
        self::bootKernel();

        $roverNavigatorController = $this->getContainer()->get(RoverNavigatorController::class);
        assert($roverNavigatorController instanceof RoverNavigatorController);

        // Act
        $actualResult = $roverNavigatorController->navigate($resourceName);
        $formattedActualResult = json_decode($actualResult->getContent(), true);

        // Assert
        $this->assertJsonResponseStructure($formattedActualResult);
        $this->assertEquals(Response::HTTP_OK, $formattedActualResult['code']);
        $this->assertEquals($expectedResult, $formattedActualResult['data']);
    }

    /**
     * @group Integration
     * @dataProvider provideInputSourceNotFoundData
     */
    public function testNavigateShouldThrowExceptionWhenInputSourceNotFound(string $resourceName, array $expectedErrorMessages): void
    {
        // Arrange
        self::bootKernel();

        $roverNavigatorController = $this->getContainer()->get(RoverNavigatorController::class);
        assert($roverNavigatorController instanceof RoverNavigatorController);

        // Act
        $actualResult = $roverNavigatorController->navigate('input-not-found.txt');
        $formattedActualResult = json_decode($actualResult->getContent(), true);

        // Assert
        $this->assertJsonResponseStructure($formattedActualResult);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $formattedActualResult['code']);
        $this->assertEquals($expectedErrorMessages, $formattedActualResult['errors']);
        $this->assertNull($formattedActualResult['data']);
    }

    /**
     * @group Integration
     * @dataProvider provideInvalidInputData
     */
    public function testNavigateShouldThrowExceptionWhenInputIsInvalid(string $resourceName, array $expectedErrorMessages): void
    {
        // Arrange
        self::bootKernel();

        $roverNavigatorController = $this->getContainer()->get(RoverNavigatorController::class);
        assert($roverNavigatorController instanceof RoverNavigatorController);

        // Act
        $actualResult = $roverNavigatorController->navigate($resourceName);
        $formattedActualResult = json_decode($actualResult->getContent(), true);

        // Assert
        $this->assertJsonResponseStructure($formattedActualResult);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $formattedActualResult['code']);
        $this->assertEquals($expectedErrorMessages, $formattedActualResult['errors']);
        $this->assertNull($formattedActualResult['data']);
    }

    /**
     * @group Integration
     * @dataProvider provideInvalidOrientationData
     */
    public function testNavigateShouldThrowExceptionWhenOrientationIsInvalid(string $resourceName, array $expectedErrorMessages): void
    {
        // Arrange
        self::bootKernel();

        $roverNavigatorController = $this->getContainer()->get(RoverNavigatorController::class);
        assert($roverNavigatorController instanceof RoverNavigatorController);

        // Act
        $actualResult = $roverNavigatorController->navigate($resourceName);
        $formattedActualResult = json_decode($actualResult->getContent(), true);

        // Assert
        $this->assertJsonResponseStructure($formattedActualResult);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $formattedActualResult['code']);
        $this->assertEquals($expectedErrorMessages, $formattedActualResult['errors']);
        $this->assertNull($formattedActualResult['data']);
    }

    /**
     * @group Integration
     * @dataProvider provideInvalidRoverCoordinateData
     */
    public function testNavigateShouldThrowExceptionWhenRoverCoordinateIsInvalid(string $resourceName, array $expectedErrorMessages): void
    {
        // Arrange
        self::bootKernel();

        $roverNavigatorController = $this->getContainer()->get(RoverNavigatorController::class);
        assert($roverNavigatorController instanceof RoverNavigatorController);

        // Act
        $actualResult = $roverNavigatorController->navigate($resourceName);
        $formattedActualResult = json_decode($actualResult->getContent(), true);

        // Assert
        $this->assertJsonResponseStructure($formattedActualResult);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $formattedActualResult['code']);
        $this->assertEquals($expectedErrorMessages, $formattedActualResult['errors']);
        $this->assertNull($formattedActualResult['data']);
    }

    /**
     * @group Integration
     * @dataProvider provideInvalidCommandKeyData
     */
    public function testNavigateShouldThrowExceptionWhenCommandKeyIsInvalid(string $resourceName, array $expectedErrorMessages): void
    {
        // Arrange
        self::bootKernel();

        $roverNavigatorController = $this->getContainer()->get(RoverNavigatorController::class);
        assert($roverNavigatorController instanceof RoverNavigatorController);

        // Act
        $actualResult = $roverNavigatorController->navigate($resourceName);
        $formattedActualResult = json_decode($actualResult->getContent(), true);

        // Assert
        $this->assertJsonResponseStructure($formattedActualResult);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $formattedActualResult['code']);
        $this->assertEquals($expectedErrorMessages, $formattedActualResult['errors']);
        $this->assertNull($formattedActualResult['data']);
    }

    private function assertJsonResponseStructure(array $formattedActualResult): void
    {
        $this->isJson();
        $this->assertArrayHasKey('status', $formattedActualResult);
        $this->assertArrayHasKey('code', $formattedActualResult);
        $this->assertArrayHasKey('message', $formattedActualResult);
        $this->assertArrayHasKey('errors', $formattedActualResult);
        $this->assertArrayHasKey('data', $formattedActualResult);
    }

    private function provideCorrectRoverPositionData(): array
    {
        return [
            'input.txt' => [
                'input.txt',
                [
                    (new RoverPosition('1 3 N'))->value,
                    (new RoverPosition('5 1 E'))->value,
                ],
            ],
            'final-coordinate-exceed-upper-right-border-input.txt' => [
                'final-coordinate-exceed-upper-right-border-input.txt',
                [
                    (new RoverPosition('5 5 N'))->value,
                    (new RoverPosition('3 5 N'))->value,
                    (new RoverPosition('5 5 N'))->value,
                ],
            ],
            'final-coordinate-exceed-bottom-left-border-input.txt' => [
                'final-coordinate-exceed-bottom-left-border-input.txt',
                [
                    (new RoverPosition('0 0 S'))->value,
                    (new RoverPosition('0 2 E'))->value,
                ],
            ],
        ];
    }

    private function provideInputSourceNotFoundData(): array
    {
        return [
            'input-not-found.txt' => [
                'input-not-found.txt',
                ['The requested input file "input-not-found.txt" was not found'],
            ],
        ];
    }

    private function provideInvalidInputData(): array
    {
        return [
            'empty-input.txt' => [
                'empty-input.txt',
                ['Input file is empty'],
            ],
            'no-command-keys-input.txt' => [
                'no-command-keys-input.txt',
                ['No command keys was provided'],
            ],
        ];
    }

    private function provideInvalidOrientationData(): array
    {
        return [
            'invalid-orientation-input.txt' => [
                'invalid-orientation-input.txt',
                ['Provided orientation key A is invalid'],
            ],
        ];
    }

    private function provideInvalidRoverCoordinateData(): array
    {
        return [
            'invalid-upper-right-border-input.txt' => [
                'invalid-upper-right-border-input.txt',
                ['Provided rover coordinate has exceeded plateau upper right border'],
            ],
            'invalid-bottom-left-border-input.txt' => [
                'invalid-bottom-left-border-input.txt',
                ['Provided rover coordinate has exceeded plateau bottom left border'],
            ],
        ];
    }

    private function provideInvalidCommandKeyData(): array
    {
        return [
            'invalid-command-key-input.txt' => [
                'invalid-command-key-input.txt',
                ['Provided command key Z is invalid'],
            ],
        ];
    }
}