<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Application\Service\ContainerParametersServiceInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final readonly class ContainerParametersService implements ContainerParametersServiceInterface
{
    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    public function getRootDir(): string
    {
        return $this->parameterBag->get('kernel.project_dir');
    }

    public function getParameter(string $parameterName): mixed
    {
        return $this->parameterBag->get($parameterName);
    }
}