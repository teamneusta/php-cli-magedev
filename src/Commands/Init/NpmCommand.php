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
 * Class: NpmCommand
 *
 * @see AbstractCommand
 */
class NpmCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName('init:npm');
        $this->setDescription('runs npm:install');

        $this->onExecute(function ($runtime) {
            $magentoVersion = $runtime->getConfig()->getMagentoVersion();
            $sourceFolder = $runtime->getConfig()->get("source_folder");
            // npm only used for magento2
            if ($magentoVersion == "2") {
                try {
                    $runtime->getDocker()->execute(
                        "bash -c \"[[ ! -f \"/usr/bin/node\" ]] && ln -s /usr/bin/nodejs /usr/bin/node\"",
                        [
                          'user' => 'root'
                        ]
                    );
                } catch (\Exception $e) {
                    // TODO: handle exception more precisly here
                    // this command may exit with return code != 0
                    // which aborts setup
                }

                // avoid ENOENT, open '/var/www/html/package.json' error
                if (!file_exists($sourceFolder . "/package.json") && file_exists($sourceFolder . "package.json.sample")) {
                    $runtime->getShell()->bash("cp ".$sourceFolder."/package.json.sample ".$sourceFolder."/package.json");
                }



                $this->execNpmCommand($runtime, "npm install -g grunt-cli");
                $this->execNpmCommand($runtime, "npm install");
            }
        });
    }

    /**
     * execNpmCommand
     *
     * @param Runtime $runtime
     * @param string $cmd
     */
    public function execNpmCommand($runtime, $cmd) {
        $config = $runtime->getConfig();
        $useProxy = $config->optionExists("proxy");
        if ($useProxy) {
            $proxy = $config->get("proxy");
            if (array_key_exists("HTTP", $proxy)) {
                $cmd = "npm config set proxy " . $proxy["HTTP"] . " && " . $cmd;
            }
            if (array_key_exists("HTTPS", $proxy)) {
                $cmd = "npm config set https-proxy " . $proxy["HTTP"] . " && " . $cmd;
            }
        }
        $runtime->getDocker()->execute(
            $cmd,
            [
                'user' => 'root'
            ]
        );
    }
}
