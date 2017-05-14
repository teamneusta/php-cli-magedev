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
    ) {
        $this->containerManager = $this->docker->getContainerManager();
        $this->imageManager = $this->docker->getImageManager();
        $this->networkManager = $this->docker->getNetworkManager();
        $this->fileHelper = $fileHelper;
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
}
