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

namespace TeamNeusta\Magedev\Docker;

use Docker\Docker;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;

/**
 * Class Context
 */
class Context
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Docker\Docker
     */
    protected $docker;

    /**
     * @var \Docker\Manager\ContainerManager
     */
    protected $containerManager;

    /**
     * @var \Docker\Manager\ImageManager
     */
    protected $imageManager;

    /**
     * @var string[]
     */
    protected $envVars = [];

    /**
     * @var \TeamNeusta\Magedev\Runtime\Helper\FileHelper
     */
    protected $fileHelper;

    /**
     * @var \Docker\Manager\NetworkManager
     */
    protected $networkManager;

    /**
     * __construct
     *
     * @param Config $config
     * @param FileHelper $fileHelper
     */
    public function __construct(
        Config $config,
        FileHelper $fileHelper
    ) {
        $this->config = $config;
        $this->docker = new Docker();
        $this->containerManager = $this->docker->getContainerManager();
        $this->imageManager = $this->docker->getImageManager();
        $this->networkManager = $this->docker->getNetworkManager();
        $this->fileHelper = $fileHelper;
    }

    /**
     * getConfig
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * buildName
     *
     * @param string $containerName
     */
    public function buildName($containerName)
    {
        $projectName = $this->config->getProjectName();
        if ($projectName !== "") {
            $projectName = '-' . $projectName;
        }

        return 'magedev' . $projectName . '-' . $containerName;
    }

    /**
     * getContainerManager
     * @return \Docker\Manager\ContainerManager
     */
    public function getContainerManager()
    {
        return $this->containerManager;
    }

    /**
     * getImageManager
     * @return \Docker\Manager\ImageManager
     */
    public function getImageManager()
    {
        return $this->imageManager;
    }

    /**
     * getNetworkManager
     * @return \Docker\Manager\Networkmanager
     */
    public function getNetworkManager()
    {
        return $this->networkManager;
    }

    /**
     * addEnv
     *
     * @param string $key
     * @param string $value
     */
    public function addEnv($key, $value)
    {
        $this->envVars[$key] = $value;
    }

    /**
     * getEnvVars
     * @return string[]
     */
    public function getEnvVars()
    {
        return $this->envVars;
    }

    /**
     * findPath
     *
     * @return FileHelper
     */
    public function getFileHelper()
    {
        return $this->fileHelper;
    }
}
