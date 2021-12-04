<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Api;

use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Entity\Parameter;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;

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
        ?string $requestModelClassName,
        string $url,
        HttpMethod $httpMethod,
        Parameter ...$parameters
    ): ApiActionTemplate {
        return new ApiActionTemplate(
            $this->locationHelper,
            $this->stringHelper,
            $operationId,
            $requestModelClassName,
            $url,
            $httpMethod,
            ...$parameters
        );
    }
}
