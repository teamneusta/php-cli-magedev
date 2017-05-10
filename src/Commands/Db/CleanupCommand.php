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

namespace TeamNeusta\Magedev\Commands\Db;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Services\ShellService;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: CleanupCommand
 *
 * @see AbstractCommand
 */
class CleanupCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Runtime\Helper\FileHelper
     */
    protected $fileHelper;

    /**
     * @var \TeamNeusta\Magedev\Services\ShellService
     */
    protected $shellService;

    /**
     * @var \TeamNeusta\Magedev\Services\DockerService
     */
    protected $dockerService;

    /**
     * __construct
     *
     * @param \TeamNeusta\Magedev\Runtime\Config $config
     * @param \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper
     * @param \TeamNeusta\Magedev\Services\ShellService $shellService
     * @param \TeamNeusta\Magedev\Services\DockerService $dockerService
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper,
        \TeamNeusta\Magedev\Services\ShellService $shellService,
        \TeamNeusta\Magedev\Services\DockerService $dockerService
    ) {
        $this->config = $config;
        $this->fileHelper = $fileHelper;
        $this->shellService = $shellService;
        $this->dockerService = $dockerService;
        parent::__construct();
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("db:cleanup");
        $this->setDescription("clean db, remove customers, orders and so on");
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $script = "cleanup.sql";

        if ($this->config->getMagentoVersion() == "1") {
            $scriptPath = $this->fileHelper->findPath("var/data/magento1/".$script);
        }
        if ($this->config->getMagentoVersion() == "2") {
            $scriptPath = $this->fileHelper->findPath("var/data/magento2/".$script);
        }

        $this->shellService->execute("cp ".$scriptPath." .");
        $this->dockerService->execute("mysql -f < ".basename($script));

        unlink($script);

        parent::execute($input, $output);
    }
}
