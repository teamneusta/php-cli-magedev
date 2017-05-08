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

use Docker\API\Model\BuildInfo;
use Docker\API\Model\Event;
use Docker\API\Model\ExecConfig;
use Docker\API\Model\ProcessConfig;
use Docker\Docker;
use Docker\Manager\ImageManager;
use Docker\Manager\MiscManager;
use TeamNeusta\Magedev\Docker\Container\Collection as ContainerCollection;

/**
 * Class Manager
 */
class Manager
{
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
     * @var \TeamNeusta\Magedev\Docker\Container\Collection
     */
    protected $containerCollection;

    /**
     * __construct
     */
    public function __construct(Docker $docker = null)
    {
        if (!$this->docker) {
            $this->docker = new Docker();
        }
        $this->containerCollection = new ContainerCollection($this);
        $this->containerManager = $this->docker->getContainerManager();
        $this->imageManager = $this->docker->getImageManager();
    }

    /**
     * containers
     * @return \TeamNeusta\Magedev\Docker\Container\Collection
     */
    public function containers()
    {
        return $this->containerCollection;
    }

    /**
     * startContainers
     */
    public function startContainers()
    {
        $this->containerCollection->eachContainer(
            function ($container) {
                $container->start();
            }
        );
    }

    /**
     * stopContainers
     */
    public function stopContainers()
    {
        $this->containerCollection->eachContainer(
            function ($container) {
                $container->stop();
            }
        );
    }

    /**
     * rebuildContainers
     */
    public function rebuildContainers()
    {
        $this->containerCollection->eachContainer(
            function ($container) {
                $container->destroy();
                if ($container->getImage() instanceof \Neusta\Magedev\Docker\Image\DockerImage) {
                    $container->getImage()->destroy();
                }
                $container->build();
            }
        );
    }

    /**
     * destroyContainers
     */
    public function destroyContainers()
    {
        $this->containerCollection->eachContainer(
            function ($container) {
                $container->destroy();
            }
        );
    }
}
