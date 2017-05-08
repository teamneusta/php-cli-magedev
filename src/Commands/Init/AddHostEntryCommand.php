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

namespace TeamNeusta\Magedev\Commands\Init;

use TeamNeusta\Magedev\Commands\AbstractCommand;

/**
 * Class: AddHostEntryCommand
 *
 * @see AbstractCommand
 */
class AddHostEntryCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName('init:add-host-entry');
        $this->setDescription('adds domain to your /etc/hosts');

        $this->onExecute(function ($runtime) {
            $config = $runtime->getConfig();

            if ($config->optionExists("domains")) {
                $domains = $config->get("domains");
                foreach ($domains as $domain => $scopeId) {
                    $this->addHostEntryIfRequired($runtime, $domain);
                }
            } else {
                $domain = $config->get("domain");
                $this->addHostEntryIfRequired($runtime, $domain);
            }
        });
    }

    /**
     * addHostEntryIfRequired
     *
     * @param Runtime $runtime
     * @param string $domain
     */
    public function addHostEntryIfRequired($runtime, $domain)
    {
        $runtime->getOutput()->writeln("checking your /etc/hosts for presence of ".$domain);

        ob_start();
        $res = system("grep \"".$domain."\" /etc/hosts");
        ob_clean();
        ob_end_flush();
        if (!$res) {
            $runtime->getOutput()->writeln("adding ".$domain." to your /etc/hosts");
            $runtime->getShell()->execute("sudo sh -c 'echo \"127.0.0.1 ".$domain."\" >> /etc/hosts'");
        }
    }
}
