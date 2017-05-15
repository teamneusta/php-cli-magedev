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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: InstallCommand
 *
 * @see AbstractCommand
 */
class InstallCommand extends AbstractCommand
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
     * __construct
     *
     * @param \TeamNeusta\Magedev\Runtime\Config $config
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
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:install");
        $this->setDescription("executes bin/magento setup:install inside container");
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $magentoVersion = $this->config->getMagentoVersion();

        // install only used for magento2
        if ($magentoVersion == "2") {
            $domain = $this->config->get("domain");

            $this->getApplication()->find('init:composer')->execute($input, $output);
            $this->getApplication()->find('init:npm')->execute($input, $output);
            $this->getApplication()->find('init:permissions')->execute($input, $output);

            $this->dockerService->execute(
                "bin/magento setup:install --db-host=mysql --db-name=magento --db-user=magento --db-password=magento --admin-user=admin --admin-password=admin123 --admin-email=admin@localhost.de --admin-firstname=admin --admin-lastname=admin --backend-frontname=admin --base-url=http://".$domain."/"
            );

            $this->getApplication()->find('init:permissions')->execute($input, $output);
            $this->getApplication()->find('config:reset')->execute($input, $output);
            $this->getApplication()->find('init:add-host-entry')->execute($input, $output);
        }
    }
}
