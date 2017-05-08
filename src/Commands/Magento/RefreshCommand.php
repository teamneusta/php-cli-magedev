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
 * Class: RefreshCommand
 *
 * @see AbstractCommand
 */
class RefreshCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:refresh");
        $this->setDescription("deletes generated files var/generation, var/di ... only Magento2");
        $this->onExecute(function ($runtime) {
            $magentoVersion = $runtime->getConfig()->getMagentoVersion();
            // only required for magento2
            if ($magentoVersion == "2") {
                foreach ([
                    "rm -rf var/generation/*",
                    "rm -rf var/di/*",
                    "rm -rf var/cache/*",
                    "rm -rf var/view_preprocessed/*",
                    "rm -rf pub/static/_requirejs",
                    "rm -rf pub/static/adminhtml",
                    "rm -rf pub/static/frontend"
                ] as $cmd) {
                    $runtime->getDocker()->execute($cmd);
                }
            }
        });
    }
}
