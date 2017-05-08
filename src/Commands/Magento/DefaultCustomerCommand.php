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
 * Class: DefaultCustomerCommand
 *
 * @see AbstractCommand
 */
class DefaultCustomerCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:customer:default");
        $this->setDescription("creates a default customer magento@neusta.de/magento@neusta.de");
        $this->onExecute(function ($runtime) {
            $config = $runtime->getConfig();
            $magentoVersion = $config->getMagentoVersion();
            $magerunHelper = $runtime->getHelper('MagerunHelper');
            $userSettings = $config->get("users");
            $customer = $userSettings["customer"];

            if (!$this->customerExists($customer["email"], $runtime)) {
                $magerunHelper->magerunCommand("customer:create ".$customer["email"]." ".$customer["password"]." ".$customer["firstname"]." ".$customer["lastname"]." default");
            } else {
                // change pw only works for magento1 for now
                if ($magentoVersion == "1") {
                    $magerunHelper->magerunCommand("customer:change-password ".$customer["email"]." ".$customer["password"]." default");
                }
            }
        });
    }

    /**
     * customerExists
     *
     * check if a given customer exists
     *
     * @param string $email
     * @param Runtime $runtime
     */
    public function customerExists($email, $runtime)
    {
        $result = $runtime->getDocker()->execute(
            "bash -c \"mysql --execute \\\"select * from customer_entity where email = '".$email."';\\\"\"",
            [
                'interactive' => false
            ]
        );

        return $result != "";
    }
}
