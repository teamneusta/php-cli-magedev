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

/**
 * Class MagerunHelper
 */
class MagerunHelper extends AbstractHelper
{
    /**
     * isMagerunInstalled
     * @return bool
     */
    public function isMagerunInstalled()
    {
        $sourceFolder = $this->runtime->getConfig()->get("source_folder");
        $fileHelper = $this->runtime->getHelper('FileHelper');
        if ($this->runtime->getConfig()->getMagentoVersion() == "1") {
            return $fileHelper->fileExists($sourceFolder . "shell/magerun");
        }
        if ($this->runtime->getConfig()->getMagentoVersion() == "2") {
            return $fileHelper->fileExists($sourceFolder . "bin/magerun");
        }
    }

    /**
     * abortIfMagerunNotInstalled
     */
    public function abortIfMagerunNotInstalled()
    {
        if (!$this->isMagerunInstalled()) {
            throw new \Exception("sorry, magerun is not installed use magento:install-magerun first");
        }
    }

    /**
     * magerunCommand
     *
     * @param string $magerunCommand
     */
    public function magerunCommand($magerunCommand)
    {
        $this->abortIfMagerunNotInstalled();
        $magentoVersion = $this->runtime->getConfig()->getMagentoVersion();

        if ($magentoVersion == "1") {
            $cmd = "shell/magerun ".$magerunCommand;

        }
        if ($magentoVersion == "2") {
            $cmd = "bin/magerun ".$magerunCommand;
        }
        $cmd = $this->runtime->getDocker()
            ->execute($cmd);
    }
}
