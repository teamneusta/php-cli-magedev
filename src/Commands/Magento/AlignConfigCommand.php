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
 * Class: AlignConfigCommand
 *
 * @see AbstractCommand
 */
class AlignConfigCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:align-config");
        $this->setDescription("changes credentials for db in env.php or local.xml in order to run this project");

        $this->onExecute(function ($runtime) {
            $config = $runtime->getConfig();
            $magentoVersion = $config->getMagentoVersion();
            $wd = $config->get("source_folder");

            if ($magentoVersion == "1") {
                $this->updateMagento1Credentials($wd, "magento", "magento", "magento", "mysql");
            }

            if ($magentoVersion == "2") {
                $this->updateMagento2Credentials($wd, "magento", "magento", "magento", "mysql");
            }
        });
    }

    /**
     * updateMagento1Credentials
     *
     * @param string $wd
     * @param string $db
     * @param string $username
     * @param string $password
     */
    public function updateMagento1Credentials($wd, $db, $username, $password, $host)
    {
        $localXml = getcwd().'/'.$wd."app/etc/local.xml";
        $content = file_get_contents($localXml);

        $content = preg_replace("/<host>[\s\S]*?<\/host>/", "<host>".$host."</host>", $content);
        $content = preg_replace("/<username>[\s\S]*?<\/username>/", "<username>".$username."</username>", $content);
        $content = preg_replace("/<password>[\s\S]*?<\/password>/", "<password>".$password."</password>", $content);
        $content = preg_replace("/<dbname>[\s\S]*?<\/dbname>/", "<dbname>".$db."</dbname>", $content);
        $content = preg_replace("/<frontName>[\s\S]*?<\/frontName>/", "<frontName>admin</frontName>", $content);

        file_put_contents($localXml, $content);
    }

    /**
     * updateMagento2Credentials
     *
     * @param string $wd
     * @param string $db
     * @param string $username
     * @param string $password
     */
    public function updateMagento2Credentials($wd, $db, $username, $password, $host)
    {
        $envFile = getcwd().'/'.$wd."app/etc/env.php";

        if (!file_exists($envFile)) {
            throw new \Exception("Your env file was not found: ".$envFile);
        }

        $data = include $envFile;

        if (!isset($data['db']['connection']['default'])) {
            throw new \Exception("no connection settings found in your env file: ".$envFile);
        }

        $data['db']['connection']['default']['host'] = $host;
        $data['db']['connection']['default']['dbname'] = $db;
        $data['db']['connection']['default']['username'] = $username;
        $data['db']['connection']['default']['password'] = $password;

        $data['db']['connection']['indexer']['host'] = $host;
        $data['db']['connection']['indexer']['dbname'] = $db;
        $data['db']['connection']['indexer']['username'] = $username;
        $data['db']['connection']['indexer']['password'] = $password;

        if (!isset($data['backend']['frontName'])) {
            throw new \Exception("no frontname setting found in your env file: ".$envFile);
        }

        $data['backend']['frontName'] = "admin";

        if (!isset($data['MAGE_MODE'])) {
            throw new \Exception("no mage mode setting found in your env file: ".$envFile);
        }
        $data['MAGE_MODE'] = "developer";

        $content = "<?php\nreturn " . var_export($data, true) . ";\n";
        file_put_contents($envFile, $content);
    }
}
