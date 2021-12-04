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
use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Mapper\ParameterMapper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Factory as TemplateFactory;
use Garden\Cli\Cli;

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT', realpath(__DIR__ . '/../'));

$args = (new Cli())
    ->description('Generates an API client from the given documentation')
    ->opt('apiJsonPath', 'Path to the api documentation JSON', true)
    ->opt('clientPath', 'Path of the client to generate', true)
    ->opt('vendorName', 'Name of the vendor the generated client belongs to', true)
    ->opt('projectName', 'Name of the project the generated client belongs to', true)
    ->opt('rootNamespace', 'Root Namespace of the project', true)
    ->parse($argv);

$composer         = new Composer($args['vendorName'], $args['projectName']);
$paths            = new Paths($args['apiJsonPath'], $args['clientPath']);
$classPaths       = new ClassPaths($args['rootNamespace']);
$fileHandler      = new FileHandler();
$configuration    = new Configuration($fileHandler, $composer, $paths, $classPaths);
$locationHelper   = new LocationHelper($configuration);
$stringHelper     = new StringHelper();
$classHelper      = new ClassHelper($stringHelper);
$typeMapper       = new TypeMapper($locationHelper, $stringHelper, $classHelper);
$parameterMapper  = new ParameterMapper($typeMapper);
$templateFactory  = new TemplateFactory($typeMapper, $locationHelper, $stringHelper);
$generatorFactory = new Factory($fileHandler, $configuration, $templateFactory, $typeMapper, $parameterMapper, $classHelper);

(new Generator($fileHandler, $configuration, $generatorFactory))->generate();
