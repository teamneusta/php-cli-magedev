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

use Symfony\Component\Console\Input\InputArgument;
use TeamNeusta\Magedev\Commands\AbstractCommand;

/**
 * Class: SetBaseUrlCommand
 *
 * @see AbstractCommand
 */
class SetBaseUrlCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:set-base-url");
        $this->setDescription("change base url of magento");
        $this->addArgument('domain', InputArgument::OPTIONAL, 'domain without http and slashes');

        $this->onExecute(function ($runtime) {
            $config = $runtime->getConfig();
            if ($config->optionExists("domains")) {
                $domains = $config->get("domains");
                foreach ($domains as $domain => $scopeId) {
                    $baseUrl = "http://".$domain."/";
                    $this->updateBaseUrl($runtime, $baseUrl, $scopeId);

                }
                return;
            } else {
                $baseUrl = "";
                if ($config->optionExists("base_url")) {
                    $baseUrl = $config->get("base_url");
                }

                if (empty($baseUrl)) {
                    $domain = $config->get("domain");
                    $baseUrl = "http://".$domain."/";
                }

                if (empty($baseUrl)) {
                    throw new \Exception("could not determine base_url");
                }
                $this->updateBaseUrl($runtime, $baseUrl);
            }

            $cacheCleanCommand = new CacheCleanCommand();
            $cacheCleanCommand->executeCommand();
        });
    }

    /**
     * updateBaseUrl
     *
     * @param Runtime $runtime
     * @param string $baseUrl
     * @param int $scopeId
     */
    public function updateBaseUrl($runtime, $baseUrl, $scopeId = 0)
    {
        $cmd = "mysql --execute=\"update core_config_data set value='".$baseUrl."' where (path='web/unsecure/base_url' OR path='web/secure/base_url') AND scope_id=".$scopeId.";\"";
        $runtime->getDocker()->execute($cmd);
    }
}
