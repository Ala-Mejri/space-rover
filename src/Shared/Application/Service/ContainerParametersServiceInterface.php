<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

interface ContainerParametersServiceInterface
{
    public function getRootDir(): string;

    public function getParameter(string $parameterName): mixed;
}