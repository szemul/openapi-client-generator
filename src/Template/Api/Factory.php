<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Api;

use DI\Container;
use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Entity\Parameter;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\Model\ActionParameterTemplate;

class Factory
{
    private Container $diContainer;

    public function __construct(Container $container)
    {
        $this->diContainer = $container;
    }

    public function getApiTemplate(string $apiTag, ApiActionTemplate ...$actions): ApiTemplate
    {
        return new ApiTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class),
            $apiTag,
            ...$actions
        );
    }

    public function getApiActionTemplate(
        string $operationId,
        string $actionParameterClassName,
        string $url,
        HttpMethod $httpMethod,
        ?bool $responseIsList,
        ?string $responseClassName
    ): ApiActionTemplate {
        return new ApiActionTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class),
            $operationId,
            $actionParameterClassName,
            $url,
            $httpMethod,
            $responseIsList,
            $responseClassName
        );
    }

    public function getActionParameterTemplate(
        string $actionParameterClassName,
        ?string $requestModelClassName,
        Parameter ...$parameters
    ): ActionParameterTemplate {
        return new ActionParameterTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class),
            $actionParameterClassName,
            $requestModelClassName,
            ...$parameters
        );
    }
}
