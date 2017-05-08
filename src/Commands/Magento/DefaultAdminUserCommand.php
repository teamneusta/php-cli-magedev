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
 * Class: DefaultAdminUserCommand
 *
 * @see AbstractCommand
 */
class DefaultAdminUserCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:admin:default");
        $this->setDescription("updates admin user to default credentials admin/admin123");
        $this->onExecute(function ($runtime) {
            $config = $runtime->getConfig();
            $magentoVersion = $config->getMagentoVersion();
            $magerunHelper = $runtime->getHelper('MagerunHelper');
            $userSettings = $config->get("users");
            $adminUser = $userSettings["admin"];

            if (!$this->userExists("admin", $runtime)) {
                if ($magentoVersion == "1") {
                    $magerunHelper->magerunCommand("admin:user:create ".$adminUser["user"]." ".$adminUser["email"]." ".$adminUser["password"]." ".$adminUser["firstname"]." ".$adminUser["lastname"]);
                } else {
                    $magerunHelper->magerunCommand("admin:user:create --admin-user=".$adminUser["user"]." --admin-email=".$adminUser["email"]." --admin-password=".$adminUser["password"]." --admin-firstname=".$adminUser["firstname"]." --admin-lastname=".$adminUser["lastname"]);
                }
            }

            $magerunHelper->magerunCommand("admin:user:change-password admin admin123");
        });
    }

    /**
     * userExists
     *
     * check if a given user is an existing magento backend user
     *
     * @param string $username
     */
    public function userExists($username, $runtime)
    {
        $result = $runtime->getDocker()->execute(
            "bash -c \"mysql --execute \\\"select * from admin_user where username = '".$username."';\\\"\"",
            [
                'interactive' => false
            ]
        );

        return $result != "";
    }
}
