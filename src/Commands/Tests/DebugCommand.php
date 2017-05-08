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

namespace TeamNeusta\Magedev\Commands\Tests;

use TeamNeusta\Magedev\Commands\AbstractCommand;

/**
 * Class: DebugCommand
 *
 * @see AbstractCommand
 */
class DebugCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("tests:debug");
        $this->setDescription("debug tests");
        $this->onExecute(function ($runtime) {
            $phpunitConfig = "/var/www/html/phpunit.xml";
            $config = $runtime->getConfig();
            if ($config->optionExists("phpunitxml_path")) {
                $phpunitConfig = $config->get("phpunitxml_path");
            }

            if ($config->optionExists("xdebug")) {
                $xdebugSettings = $config->get("xdebug");
                $remoteHost = $xdebugSettings["remote_host"];
                $ideKey = $xdebugSettings["idekey"];
                if ($ideKey && $remoteHost) {
                    $cmd  = "XDEBUG_CONFIG=\"idekey=" . $idekey . "\"";
                    $cmd .= " php -dxdebug.remote_host=" . $remoteHost;
                    $cmd .= " -dxdebug.remote_enable=on vendor/bin/phpunit";
                    $cmd .= " -c ".$phpunitConfig;
                    $runtime->getDocker()->execute($cmd);
                    return;
                }
            }
            throw new \Exception("xdebug settings not found");
        });
    }
}
