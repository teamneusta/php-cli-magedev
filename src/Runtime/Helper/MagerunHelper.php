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

namespace TeamNeusta\Magedev\Runtime\Helper;

use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class MagerunHelper.
 */
class MagerunHelper
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
     * @var \TeamNeusta\Magedev\Services\DockerService
     */
    protected $dockerService;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config            $config
     * @param \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper
     * @param \TeamNeusta\Magedev\Services\DockerService    $dockerService
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper,
        \TeamNeusta\Magedev\Services\DockerService $dockerService
    ) {
        $this->config = $config;
        $this->fileHelper = $fileHelper;
        $this->dockerService = $dockerService;
    }

    /**
     * isMagerunInstalled.
     *
     * @return bool
     */
    public function isMagerunInstalled()
    {
        $sourceFolder = $this->config->get('source_folder');
        if ($this->config->getMagentoVersion() == '1') {
            return $this->fileHelper->fileExists($sourceFolder.'shell/magerun');
        }
        if ($this->config->getMagentoVersion() == '2') {
            return $this->fileHelper->fileExists($sourceFolder.'bin/magerun');
        }
    }

    /**
     * abortIfMagerunNotInstalled.
     */
    public function abortIfMagerunNotInstalled()
    {
        if (!$this->isMagerunInstalled()) {
            throw new \Exception('sorry, magerun is not installed use magento:install-magerun first');
        }
    }

    /**
     * magerunCommand.
     *
     * @param string $magerunCommand
     */
    public function magerunCommand($magerunCommand)
    {
        $this->abortIfMagerunNotInstalled();
        $magentoVersion = $this->config->getMagentoVersion();

        if ($magentoVersion == '1') {
            $cmd = 'shell/magerun '.$magerunCommand;
        }
        if ($magentoVersion == '2') {
            $cmd = 'bin/magerun '.$magerunCommand;
        }
        $this->dockerService->execute($cmd);
    }
}
