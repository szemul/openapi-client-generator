<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Model;

use Emul\OpenApiClientGenerator\Template\Model\ResponseListTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class ResponseListTemplateTest extends TemplateTestCaseAbstract
{
    private string $itemClassName = 'ItemClass';

    public function testToString_shouldGenerateClass()
    {
        $sut = $this->getSut();

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            <?php

            declare(strict_types=1);

            namespace Root\Model;

            use JsonSerializable;
            
            class ItemClassList implements ResponseListInterface, ResponseInterface, JsonSerializable
            {
                use ResponseTrait;
            
                /** @var ItemClass[] */
                private array $items = [];

                public function getItemClass(): string
                {
                    return ItemClass::class;
                }

                public function add(ItemClass $item): self
                {
                    $this->items[] = $item;

                    return $this;
                }

                /**
                 * @return ItemClass[]
                 */
                public function getItems(): array
                {
                    return $this->items;
                }
                
                public function jsonSerialize(): mixed
                {
                    return $this->items;
                }
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    public function testGetDirectory()
    {
        $directory = $this->getSut()->getDirectory();

        $this->assertSame('/src/Model/', $directory);
    }

    public function testGetClassname()
    {
        $className = $this->getSut()->getClassName(true);

        $this->assertSame('Root\Model\ItemClassList', $className);
    }

    private function getSut(): ResponseListTemplate
    {
        return new ResponseListTemplate($this->locationHelper, $this->stringHelper, $this->itemClassName);
    }
}
