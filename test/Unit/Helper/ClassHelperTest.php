<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Helper;

use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;

class ClassHelperTest extends TestCaseAbstract
{
    public function testGetActionParameterClassName()
    {
        $tag         = 'customer';
        $operationId = 'getList';

        $result = $this->getSut()->getActionParameterClassName($tag, $operationId);

        $this->assertSame('CustomerGetList', $result);
    }

    public function testGetModelClassName()
    {
        $reference = '#/components/schemas/internal.customer-get_list.response';

        $result = $this->getSut()->getModelClassName($reference);

        $this->assertSame('InternalCustomerGetListResponse', $result);
    }

    public function testGetListModelClassName()
    {
        $reference = '#/components/schemas/internal.customer-get_list.response';

        $result = $this->getSut()->getListModelClassname($reference);

        $this->assertSame('InternalCustomerGetListResponseList', $result);
    }

    private function getSut(): ClassHelper
    {
        return new ClassHelper(new StringHelper());
    }
}
