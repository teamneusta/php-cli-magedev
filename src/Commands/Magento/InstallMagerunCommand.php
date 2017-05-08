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

use TeamNeusta\Magedev\Commands\AbstractCommand;

/**
 * Class: InstallMagerunCommand
 *
 * @see AbstractCommand
 */
class InstallMagerunCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:install-magerun");
        $this->setDescription("installs current magerun into bin/magerun or shell/magerun");

        $this->onExecute(function ($runtime) {
            $config = $runtime->getConfig();
            $magentoRoot = $config->get('source_folder');
            $magentoVersion = $config->getMagentoVersion();

            if ($magentoVersion == "1") {
                $runtime->getShell()->wd($magentoRoot)->bash("curl -O https://files.magerun.net/n98-magerun.phar");
                $runtime->getShell()->wd($magentoRoot)->bash("mv n98-magerun.phar shell/magerun");
                $runtime->getShell()->wd($magentoRoot)->bash("chmod +x shell/magerun");
            }
            if ($magentoVersion == "2") {
                $runtime->getShell()->wd($magentoRoot)->bash("curl -O https://files.magerun.net/n98-magerun2.phar");
                $runtime->getShell()->wd($magentoRoot)->bash("cp n98-magerun2.phar bin/magerun");
                $runtime->getShell()->wd($magentoRoot)->bash("chmod +x bin/magerun");
            }
        });
    }
}
