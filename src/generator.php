<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator;

use Emul\OpenApiClientGenerator\Configuration\ClassPaths;
use Emul\OpenApiClientGenerator\Configuration\Composer;
use Emul\OpenApiClientGenerator\Configuration\Paths;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Generator\Factory;
use Emul\OpenApiClientGenerator\Generator\Generator;
use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Factory as TemplateFactory;

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT', realpath(__DIR__ . '/../'));

$apiDocPath = __DIR__ . '/../../../shoppinpal/food/order-distributor/app/internal_api/www/openapi.json';
$targetPath = __DIR__ . '/../../../shoppinpal/food/generator_output/';

$fileHandler      = new FileHandler();
$composer         = new Composer('shoppinpal', 'test-api-client');
$paths            = new Paths($apiDocPath, $targetPath);
$classPaths       = new ClassPaths('Test');
$configuration    = new Configuration($fileHandler, $composer, $paths, $classPaths);
$locationHelper   = new LocationHelper($configuration);
$stringHelper     = new StringHelper();
$typeMapper       = new TypeMapper($locationHelper, $stringHelper);
$templateFactory  = new TemplateFactory($typeMapper, $locationHelper, $stringHelper);
$generatorFactory = new Factory($fileHandler, $configuration, $templateFactory, $typeMapper);

(new Generator($fileHandler, $configuration, $generatorFactory))->generate();
