<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator;

use Emul\OpenApiClientGenerator\Generator\Generator;

require_once __DIR__ . '/../vendor/autoload.php';

$apiDocPath = __DIR__ . '/../../../shoppinpal/food/order-distributor/app/internal_api/www/openapi.json';
$targetPath = __DIR__ . '/../../../shoppinpal/food/generator_output/';

(new Generator($apiDocPath, $targetPath, 'Test'))->generate();

