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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: ResetCommand.
 *
 * @see AbstractCommand
 */
class ResetCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Runtime\Helper\FileHelper
     */
    protected $fileHelper;

    /**
     * @var \TeamNeusta\Magedev\Services\DockerService
     */
    protected $dockerService;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config            $config
     * @param \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper
     * @param \TeamNeusta\Magedev\Services\DockerService    $dockerService
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper,
        \TeamNeusta\Magedev\Services\DockerService $dockerService
    ) {
        $this->config = $config;
        $this->fileHelper = $fileHelper;
        $this->dockerService = $dockerService;
        parent::__construct();
    }
    /**
     * configure.
     */
    protected function configure()
    {
        $this->setName('config:reset');
        $this->setDescription('resets known values in core_config_data to dev defaults');
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $magentoVersion = $this->config->getMagentoVersion();

        if ($magentoVersion == '1') {
            $configDefault = $this->fileHelper->findPath('var/data/magento1/config.yml');
        }
        if ($magentoVersion == '2') {
            $configDefault = $this->fileHelper->findPath('var/data/magento2/config.yml');
        }

        if ($this->fileHelper->fileExists($configDefault)) {
            $data = Yaml::parse($this->fileHelper->read($configDefault));

            foreach ($data as $key => $value) {
                $this->updateConfigValue($key, $value);
            }
        }
    }

    /**
     * updateConfigValue.
     *
     * @param string $key
     * @param string $value
     */
    public function updateConfigValue($key, $value)
    {
        if (!$this->configExists($key)) {
            $sql = "INSERT core_config_data (scope, scope_id, path, value) VALUES ('default', 0, '".$key."', ".$value.');';
        } else {
            $sql = "UPDATE core_config_data SET value='".$value."' WHERE path='".$key."'";
        }
        $this->dockerService->execute(
            'mysql --execute "'.$sql.'"',
            [
                'interactive' => false,
            ]
        );
    }

    /**
     * configExists.
     *
     * @param string $path
     */
    public function configExists($path)
    {
        $result = $this->dockerService->execute(
            "mysql --execute \"select * from core_config_data where path = '".$path."';\"",
            [
                'interactive' => false,
            ]
        );

        return $result != '';
    }
}
