<?php

namespace Emul\OpenApiClientGenerator\Command;

use DI\Container;
use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Configuration\Factory as ConfigFactory;
use Emul\OpenApiClientGenerator\Generator\Factory as GeneratorFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'client-generator',
    description: 'Generates an API client from the given documentation',
    hidden: false
)]
class GeneratorCommand extends Command
{
    public function __construct(
        private readonly ConfigFactory $configFactory,
        private readonly GeneratorFactory $generatorFactory,
        private Container $container
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->configFactory->getConfig(
            $input->getOption('vendor-name'),
            $input->getOption('project-name'),
            $input->getOption('api-json-path'),
            $input->getOption('client-path'),
            $input->getOption('root-namespace')
        );

        $this->container->set(Configuration::class, $config);

        $generator = $this->generatorFactory->getGenerator($config);

        $generator->generate();

        return Command::SUCCESS;
    }

    protected function configure()
    {
        $this
            ->addOption('api-json-path', null, InputOption::VALUE_REQUIRED, 'Path to the api documentation JSON')
            ->addOption('client-path', null, InputOption::VALUE_REQUIRED, 'Path of the client to generate')
            ->addOption(
                'vendor-name',
                null,
                InputOption::VALUE_REQUIRED,
                'Name of the vendor the generated client belongs to'
            )
            ->addOption(
                'project-name',
                null,
                InputOption::VALUE_REQUIRED,
                'Name of the project the generated client belongs to'
            )
            ->addOption('root-namespace', null, InputOption::VALUE_REQUIRED, 'Root Namespace of the project');
    }
}