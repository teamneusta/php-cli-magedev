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

namespace TeamNeusta\Magedev\Commands\Config;

use Symfony\Component\Yaml\Yaml;
use TeamNeusta\Magedev\Commands\AbstractCommand;

/**
 * Class: ResetCommand
 *
 * @see AbstractCommand
 */
class ResetCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("config:reset");
        $this->setDescription("resets known values in core_config_data to dev defaults");

        $this->onExecute(function ($runtime) {
            $fileHelper = $runtime->getHelper('FileHelper');
            $magentoVersion = $runtime->getConfig()->getMagentoVersion();

            if ($magentoVersion == "1") {
                $configDefault = $fileHelper->findPath("var/data/magento1/config.yml");
            }
            if ($magentoVersion == "2") {
                $configDefault = $fileHelper->findPath("var/data/magento2/config.yml");
            }

            if (file_exists($configDefault)) {
                $data = Yaml::parse($runtime->getHelper('FileHelper')->read($configDefault));

                foreach ($data as $key => $value) {
                    $this->updateConfigValue($key, $value, $runtime);
                }
            }
        });
    }

    /**
     * updateConfigValue
     *
     * @param string $key
     * @param string $value
     * @param Runtime $runtime
     */
    public function updateConfigValue($key, $value, $runtime)
    {
        if (!$this->configExists($key, $runtime)) {
            $sql = "INSERT core_config_data (scope, scope_id, path, value) VALUES ('default', 0, '".$key."', ".$value.");";
        } else {
            $sql = "UPDATE core_config_data SET value='".$value."' WHERE path='".$key."'";
        }
        $runtime->getDocker()->execute(
            "bash -c \"mysql --execute \\\"".$sql."\\\"\"",
            [
                'interactive' => false
            ]
        );
    }

    /**
     * configExists
     *
     * @param string $path
     * @param Runtime $runtime
     */
    public function configExists($path, $runtime)
    {
        $result = $runtime->getDocker()->execute(
            "bash -c \"mysql --execute \\\"select * from core_config_data where path = '".$path."';\\\"\"",
            [
                'interactive' => false
            ]
        );
        return $result != "";
    }
}
