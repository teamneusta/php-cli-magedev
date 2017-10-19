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

namespace TeamNeusta\Magedev\Commands\Init;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: PermissionsCommand.
 *
 * @see AbstractCommand
 */
class PermissionsCommand extends AbstractCommand
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
        $this->setName('init:permissions');
        $this->setDescription('set file and folder permissions for magento');
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $sourceFolder = $this->config->get('source_folder');

        // use current folder, if it is the current one
        if (empty($sourceFolder)) {
            $sourceFolder = '.';
        }

        $commands = [
            'mkdir -p /var/www/html',
            'mkdir -p /var/www/.composer',
            'mkdir -p /var/www/.ssh',
            'mkdir -p /var/www/modules',
            'mkdir -p /var/www/composer-cache',
            'chown -R www-data:users /var/www/html',
            'chown -R www-data:users /var/www/.composer',
            'chown -R www-data:users /var/www/.ssh',
            'chown -R www-data:users /var/www/modules',
            'chown -R www-data:users /var/www/composer-cache',
            // TODO: more fine grained permissions
            'cd /var/www/html && chmod -R 775 '.$sourceFolder,
        ];

        foreach ($commands as $cmd) {
            $this->dockerService->execute(
                $cmd,
                [
                  'user' => 'root',
                ]
            );
        }
        $this->dockerService->execute(
            'usermod -u '.getmyuid().' mysql',
            [
                'user' => 'root',
                'container' => 'mysql',
            ]
        );
    }
}
