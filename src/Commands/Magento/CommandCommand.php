<?php
/**
 * This file is part of the teamneusta/php-cli-magedev package.
 *
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @license https://opensource.org/licenses/mit-license MIT License
 */

namespace TeamNeusta\Magedev\Commands\Magento;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: CacheCleanCommand.
 *
 * @see AbstractCommand
 */
class CommandCommand extends AbstractCommand
{
    const ARGUMENT_MAGENTO_COMMAND = 'magento_command';
    const ARGUMENT_MAGENTO_COMMAND_ARGS = 'magento_command_args';

    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Services\DockerService
     */
    protected $dockerService;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config         $config
     * @param \TeamNeusta\Magedev\Services\DockerService $dockerService
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Services\DockerService $dockerService
    ) {
        $this->config = $config;
        $this->dockerService = $dockerService;
        parent::__construct();
    }

    /**
     * configure.
     */
    protected function configure()
    {
        $this->setName('magento:command')
            ->setDescription('Execute command with bin/magento (magento 2 only)');

        $this->addArgument(
            self::ARGUMENT_MAGENTO_COMMAND,
            InputArgument::REQUIRED,
            'Command to be executed by bin/magento'
        );
        $this->addArgument(
            self::ARGUMENT_MAGENTO_COMMAND_ARGS,
            InputArgument::IS_ARRAY,
            'parameters for bin/magento'
        );
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $input->getArgument(self::ARGUMENT_MAGENTO_COMMAND);
        $arguments = $input->getArgument(self::ARGUMENT_MAGENTO_COMMAND_ARGS);

        $shellCommand = sprintf(
            'bin/magento %s %s',
            $command,
            implode(' ', $arguments)
        );

        if ($this->config->getMagentoVersion() == '2') {
            $this->dockerService->execute($shellCommand);
        } else {
            $this->output->writeln('<error>This command works for magento 2 only!</error>');
        }
    }
}
