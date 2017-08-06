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
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class: ModuleVersionCommand
 *
 * @see AbstractCommand
 */
class ModuleVersionCommand extends AbstractCommand
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
     * @param \TeamNeusta\Magedev\Services\ShellService $shellService
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
        $this->setName("magento:module:version");
        $this->setDescription("set version of module in database");
        $this->addArgument('module', InputArgument::REQUIRED, 'Module e.g. Magento_Catalog');
        $this->addArgument('version', InputArgument::REQUIRED, 'Version e.g. 1.0.1');
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
        if ($magentoVersion == "2") {
            $module = $this->config->get("module");
            $version = $this->config->get("version");
            $this->updateModuleVersion($module, $version);
        }
    }

    /**
     * updateModuleVersion
     *
     * @param string $baseUrl
     * @param int $scopeId
     */
    public function updateModuleVersion($module, $version)
    {
        $cmd = "mysql --execute=\"update setup_module set ";
        $cmd .= "schema_version='".$version."', data_version='".$version."'";
        $cmd .= "where module='".$module."';\"";
        $this->dockerService->execute($cmd);
    }
}
