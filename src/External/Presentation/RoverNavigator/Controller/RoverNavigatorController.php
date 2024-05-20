<?php

declare(strict_types=1);

namespace App\External\Presentation\RoverNavigator\Controller;

use App\Core\Application\RoverPosition\Output\RoverPositionOutputInterface;
use App\Core\Application\RoverPosition\Service\RoverPositionCollectionBuilder;
use App\Core\Domain\Command\Exception\InvalidCommandKeyException;
use App\Core\Domain\Orientation\Exception\InvalidOrientationException;
use App\Core\Domain\Rover\Exception\InvalidRoverCoordinateException;
use App\External\Presentation\RoverNavigator\Input\RoverInputParserInterface;
use App\External\Presentation\RoverNavigator\Input\RoverInputSourceReaderInterface;
use App\Shared\Presentation\Exception\InputSourceNotFoundException;
use App\Shared\Presentation\Exception\InvalidInputException;
use App\Shared\Presentation\Response\ResponseInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RoverNavigatorController extends AbstractController
{
    public function __construct(
        private readonly RoverInputParserInterface       $roverInputParser,
        private readonly RoverInputSourceReaderInterface $roverInputSourceReader,
        private readonly RoverPositionOutputInterface    $roverPositionOutput,
        private readonly RoverPositionCollectionBuilder  $roverPositionCollectionBuilder,
        private readonly ResponseInterface               $response,
        private readonly LoggerInterface                 $logger,
    )
    {
    }

    #[Route('/api/rover/navigate/{resourceName}', name: 'api_rover_navigate')]
    public function navigate(string $resourceName): Response
    {
        try {
            $input = $this->roverInputSourceReader->getData($resourceName);
            $roverNavigatorInput = $this->roverInputParser->parseData($input);
            $roverPositionCollection = $this->roverPositionCollectionBuilder->build($roverNavigatorInput);

            $this->roverPositionOutput->saveData($roverPositionCollection);
        } catch (InputSourceNotFoundException $exception) {
            return $this->response->notFound([$exception->getMessage()]);
        } catch (InvalidInputException|InvalidOrientationException|InvalidRoverCoordinateException|InvalidCommandKeyException $exception) {
            return $this->response->validationError([$exception->getMessage()]);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                'resourceName' => $resourceName,
                'exceptionCode' => $exception->getCode(),
            ]);

            return $this->response->error(['An unexpected error occurred!']);
        }

        return $this->response->success($roverPositionCollection->getArrayCopy());
    }
}