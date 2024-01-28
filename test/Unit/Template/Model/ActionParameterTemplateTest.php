<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Model;

use Emul\OpenApiClientGenerator\Entity\Parameter;
use Emul\OpenApiClientGenerator\Entity\ParameterType;
use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Template\Model\ActionParameterTemplate;
use Emul\OpenApiClientGenerator\Template\Model\ModelPropertyTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class ActionParameterTemplateTest extends TemplateTestCaseAbstract
{
    private string     $className = 'ParameterClass';

    public function testToString_shouldGenerateClass()
    {
        $result         = (string)$this->getSut(null);
        $expectedResult = <<<'EXPECTED'
            <?php
            declare(strict_types=1);

            namespace Root\Model\ActionParameter;

            class ParameterClass
            {
                public function __construct()
                {
                }

                public function getPathParameterGetters(): array
                {
                    return [];
                }

                public function getQueryParameterGetters(): array
                {
                    return [];
                }

                public function getHeaderParameterGetters(): array
                {
                    return [];
                }

                public function hasRequestModel(): bool
                {
                    return false;
                }
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    public function testToStringWhenModelClassNameGiven_shouldGenerateClass()
    {
        $pathParameter   = new Parameter('pathParam', ParameterType::path(), true, PropertyType::string(), 'Path');
        $queryParameter  = new Parameter('query_param', ParameterType::query(), true, PropertyType::string(), 'Query');
        $headerParameter = new Parameter('headerParam', ParameterType::header(), true, PropertyType::string(), 'Header');

        $result         = (string)$this->getSut('ModelClass', $pathParameter, $queryParameter, $headerParameter);
        $expectedResult = <<<'EXPECTED'
            <?php
            
            declare(strict_types=1);
            
            namespace Root\Model\ActionParameter;
            
            use Root\Model\ModelClass;
            
            class ParameterClass
            {
                private ModelClass $requestModel;
                private string     $pathPathParam;
                private string     $queryQueryParam;
                private string     $headerHeaderParam;
            
                public function __construct(ModelClass $requestModel, string $pathParam, string $queryParam, string $headerParam)
                {
                    $this->requestModel = $requestModel;
                    $this->pathPathParam = $pathParam;
                    $this->queryQueryParam = $queryParam;
                    $this->headerHeaderParam = $headerParam;
                }
            
                public function getPathParameterGetters(): array
                {
                    return ['pathParam' => 'getPathPathParam'];
                }
            
                public function getQueryParameterGetters(): array
                {
                    return ['query_param' => 'getQueryQueryParam'];
                }
            
                public function getHeaderParameterGetters(): array
                {
                    return ['headerParam' => 'getHeaderHeaderParam'];
                }
            
                public function getRequestModel(): ModelClass
                {
                    return $this->requestModel;
                }
            
                public function getPathPathParam(): string
                {
                    return $this->pathPathParam;
                }
            
                public function getQueryQueryParam(): string
                {
                    return $this->queryQueryParam;
                }
            
                public function getHeaderHeaderParam(): string
                {
                    return $this->headerHeaderParam;
                }
            
                public function setRequestModel(ModelClass $model): self
                {
                    $this->requestModel = $model;
            
                    return $this;
                }
            
                public function setPathPathParam(string $parameter): self
                {
                    $this->pathPathParam = $parameter;
            
                    return $this;
                }
            
                public function setQueryQueryParam(string $parameter): self
                {
                    $this->queryQueryParam = $parameter;
            
                    return $this;
                }
            
                public function setHeaderHeaderParam(string $parameter): self
                {
                    $this->headerHeaderParam = $parameter;
            
                    return $this;
                }
            
                public function hasRequestModel(): bool
                {
                    return true;
                }
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    public function testGetDirectory()
    {
        $directory = $this->getSut(null)->getDirectory();

        $this->assertSame('/src/Model/ActionParameter/', $directory);
    }

    public function testGetClassname()
    {
        $className = $this->getSut(null)->getClassName(true);

        $this->assertSame('Root\Model\ActionParameter\ParameterClass', $className);
    }

    private function getPropertyTemplate(string $name, PropertyType $type, bool $isRequired, ?string $description): ModelPropertyTemplate
    {
        return new ModelPropertyTemplate(
            $this->typeMapper,
            $name,
            $type,
            $isRequired,
            $description
        );
    }

    private function getSut(?string $requestModelClassName, Parameter ...$parameters): ActionParameterTemplate
    {
        return new ActionParameterTemplate(
            $this->locationHelper,
            $this->stringHelper,
            $this->className,
            $requestModelClassName,
            ...$parameters
        );
    }
}
