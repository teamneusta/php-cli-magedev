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
 * Class: CacheCleanCommand
 *
 * @see AbstractCommand
 */
class CacheCleanCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:cache:clean");
        $this->setDescription("cleans magento cache");
        $this->onExecute(function ($runtime) {
            $magentoVersion = $runtime->getConfig()->getMagentoVersion();
            $magerunHelper = $runtime->getHelper('MagerunHelper');
            if ($magentoVersion == "1") {
                $magerunHelper->magerunCommand("cache:clean");
            }

            if ($magentoVersion == "2") {
                $runtime->getDocker()->execute("bin/magento cache:clean");
            }
        });
    }
}
