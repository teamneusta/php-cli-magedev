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

namespace TeamNeusta\Magedev\Commands\Tests;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: DebugCommand.
 *
 * @see AbstractCommand
 */
class DebugCommand extends AbstractCommand
{
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
        $this->setName('tests:debug');
        $this->setDescription('debug tests');
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $phpunitConfig = '/var/www/html/phpunit.xml';
        if ($this->config->optionExists('phpunitxml_path')) {
            $phpunitConfig = $this->config->get('phpunitxml_path');
        }

        if ($this->config->optionExists('xdebug')) {
            $xdebugSettings = $this->config->get('xdebug');
            $remoteHost = $this->dockerService->getConfig()->get('gateway');
            $ideKey = $xdebugSettings['idekey'];
            if ($ideKey && $remoteHost) {
                $cmd = 'XDEBUG_CONFIG="idekey='.$ideKey.'"';
                $cmd .= ' php -dxdebug.remote_host='.$remoteHost;
                $cmd .= ' -dxdebug.remote_enable=on vendor/bin/phpunit';
                $cmd .= ' -c '.$phpunitConfig;
                $this->dockerService->execute($cmd);

                return;
            }
        }
        throw new \Exception('xdebug settings not found');
    }
}
