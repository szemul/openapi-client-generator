<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Api;

use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Entity\Parameter;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\Model\ActionParameterTemplate;

class Factory
{
    private LocationHelper $locationHelper;
    private StringHelper   $stringHelper;

    public function __construct(LocationHelper $locationHelper, StringHelper $stringHelper)
    {
        $this->locationHelper = $locationHelper;
        $this->stringHelper   = $stringHelper;
    }

    public function getApiTemplate(string $apiTag, ApiActionTemplate ...$actions): ApiTemplate
    {
        return new ApiTemplate($this->locationHelper, $this->stringHelper, $apiTag, ...$actions);
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
            $this->locationHelper,
            $this->stringHelper,
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
            $this->locationHelper,
            $this->stringHelper,
            $actionParameterClassName,
            $requestModelClassName,
            ...$parameters
        );
    }
}
