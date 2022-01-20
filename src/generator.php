<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator;

use DI\Container;
use DI\ContainerBuilder;
use Emul\OpenApiClientGenerator\Configuration\ClassPaths;
use Emul\OpenApiClientGenerator\Configuration\Composer;
use Emul\OpenApiClientGenerator\Configuration\Paths;
use Emul\OpenApiClientGenerator\Generator\Generator;
use Garden\Cli\Cli;

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT', realpath(__DIR__ . '/../'));

$args = (new Cli())
    ->description('Generates an API client from the given documentation')
    ->opt('api-json-path', 'Path to the api documentation JSON', true)
    ->opt('client-path', 'Path of the client to generate', true)
    ->opt('vendor-name', 'Name of the vendor the generated client belongs to', true)
    ->opt('project-name', 'Name of the project the generated client belongs to', true)
    ->opt('root-namespace', 'Root Namespace of the project', true)
    ->parse($argv);

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(
    [
        Composer::class   => fn(Container $container) => new Composer($args['vendor-name'], $args['project-name']),
        Paths::class      => fn(Container $container) => new Paths($args['api-json-path'], $args['client-path']),
        ClassPaths::class => fn(Container $container) => new ClassPaths($args['root-namespace']),
    ]
);
$container = $containerBuilder->build();

/** @var Generator $generator */
$generator = $container->get(Generator::class);

$generator->generate();
