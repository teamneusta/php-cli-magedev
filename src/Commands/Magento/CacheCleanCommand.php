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
use TeamNeusta\Magedev\Runtime\Helper\MagerunHelper;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: CacheCleanCommand
 *
 * @see AbstractCommand
 */
class CacheCleanCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Runtime\Helper\MagerunHelper
     */
    protected $magerunHelper;

    /**
     * @var \TeamNeusta\Magedev\Services\DockerService
     */
    protected $dockerService;

    /**
     * __construct
     *
     * @param \TeamNeusta\Magedev\Runtime\Config $config
     * @param \TeamNeusta\Magedev\Runtime\Helper\MagerunHelper $magerunHelper
     * @param \TeamNeusta\Magedev\Services\DockerService $dockerService
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Runtime\Helper\MagerunHelper $magerunHelper,
        \TeamNeusta\Magedev\Services\DockerService $dockerService
    ) {
        $this->config = $config;
        $this->magerunHelper = $magerunHelper;
        $this->dockerService = $dockerService;
        parent::__construct();
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:cache:clean");
        $this->setDescription("cleans magento cache");
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
        if ($magentoVersion == "1") {
            $this->magerunHelper->magerunCommand("cache:clean");
        }

        if ($magentoVersion == "2") {
            $this->dockerService->execute("bin/magento cache:clean");
        }
    }
}
