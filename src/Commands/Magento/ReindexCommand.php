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
 * Class: ReindexCommand
 *
 * @see AbstractCommand
 */
class ReindexCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:reindex");
        $this->setDescription("executes bin/magento indexer:reindex inside container");

        $this->onExecute(function ($runtime) {
            $magentoVersion = $runtime->getConfig()->getMagentoVersion() ;
            $magerunHelper = $runtime->getHelper('MagerunHelper');
            try {
                if ($magentoVersion == "1") {
                    $magerunHelper->magerunCommand("index:reindex:all");
                }

                if ($magentoVersion == "2") {
                    $runtime->getDocker()
                        ->execute("bin/magento indexer:reindex");
                }
            } catch (\Exception $e) {
                // command may fail e.g.:
                // index is locked by another reindex process. Skipping.
                // do not die here
                $runtime->getOutput($e->getMessage());
            }
        });
    }
}
