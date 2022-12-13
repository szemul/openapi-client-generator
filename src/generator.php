<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator;

use DI\ContainerBuilder;
use Emul\OpenApiClientGenerator\Command\GeneratorCommand;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT', realpath(__DIR__ . '/../'));

$container        = (new ContainerBuilder())->build();
$generatorCommand = $container->get(GeneratorCommand::class);

$app = new Application();
$app->addCommands([$generatorCommand]);
$app->run();
