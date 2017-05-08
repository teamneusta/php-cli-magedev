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
 * Class: PermissionsCommand
 *
 * @see AbstractCommand
 */
class PermissionsCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("init:permissions");
        $this->setDescription("set file and folder permissions for magento");
        $this->onExecute(function ($runtime) {
            $config = $runtime->getConfig();
            $sourceFolder = $config->get("source_folder");

            // use current folder, if it is the current one
            if (empty($sourceFolder)) {
                $sourceFolder = ".";
            }

            $commands = [
                "chown -R www-data:users /var/www/html",
                "chown -R www-data:users /var/www/.composer",
                "chown -R www-data:users /var/www/.ssh",
                "chown -R www-data:users /var/www/modules",
                "chown -R www-data:users /var/www/composer-cache",
                // TODO: more fine grained permissions
                "cd /var/www/html && chmod -R 775 ".$sourceFolder
            ];

            foreach ($commands as $cmd) {
                $runtime->getDocker()->execute(
                    $cmd,
                    [
                      'user' => 'root'
                    ]
                );
            }
            $runtime->getDocker()->execute(
                "usermod -u ".getmyuid()." mysql",
                [
                    'user' => 'root',
                    'container' => 'mysql'
                ]
            );
        });
    }
}
