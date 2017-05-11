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
use TeamNeusta\Magedev\Services\ShellService;

/**
 * Class: InstallMagerunCommand
 *
 * @see AbstractCommand
 */
class InstallMagerunCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Services\ShellService
     */
    protected $shellService;


    /**
     * __construct
     *
     * @param \TeamNeusta\Magedev\Runtime\Config $config
     * @param \TeamNeusta\Magedev\Services\ShellService $shellService
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Services\ShellService $shellService
    ) {
        $this->config = $config;
        $this->shellService = $shellService;
        parent::__construct();
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:install-magerun");
        $this->setDescription("installs current magerun into bin/magerun or shell/magerun");
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $magentoRoot = $this->config->get('source_folder');
        $magentoVersion = $this->config->getMagentoVersion();

        if ($magentoVersion == "1") {
            $this->shellService->wd($magentoRoot)->bash("curl -O https://files.magerun.net/n98-magerun.phar");
            $this->shellService->wd($magentoRoot)->bash("mv n98-magerun.phar shell/magerun");
            $this->shellService->wd($magentoRoot)->bash("chmod +x shell/magerun");
        }
        if ($magentoVersion == "2") {
            $this->shellService->wd($magentoRoot)->bash("curl -O https://files.magerun.net/n98-magerun2.phar");
            $this->shellService->wd($magentoRoot)->bash("mv n98-magerun2.phar bin/magerun");
            $this->shellService->wd($magentoRoot)->bash("chmod +x bin/magerun");
        }
    }
}
