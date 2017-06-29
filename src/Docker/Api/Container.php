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

namespace TeamNeusta\Magedev\Docker\Api;

use Docker\Docker;
use Docker\Manager\ContainerManager;
use TeamNeusta\Magedev\Docker\Container\AbstractContainer;

/**
 * Class Container.
 */
class Container
{
    /**
     * @var \Docker\Manager\ContainerManager
     */
    protected $containerManager;

    /**
     * @var \TeamNeusta\Magedev\Docker\Image\Factory
     */
    protected $imageFactory;

    /**
     * @var \TeamNeusta\Magedev\Docker\Api\ImageFactory
     */
    protected $imageApiFactory;

    /**
     * @var \TeamNeusta\Magedev\Docker\Container\AbstractContainer
     */
    protected $container;

    /**
     * __construct.
     *
     * @param \Docker\Manager\ContainerManager                       $containerManager
     * @param \TeamNeusta\Magedev\Docker\Image\Factory               $imageFactory
     * @param \TeamNeusta\Magedev\Docker\Api\ImageFactory            $imageApi
     * @param \TeamNeusta\Magedev\Docker\Container\AbstractContainer $container
     */
    public function __construct(
        \Docker\Manager\ContainerManager $containerManager,
        \TeamNeusta\Magedev\Docker\Image\Factory $imageFactory,
        \TeamNeusta\Magedev\Docker\Api\ImageFactory $imageApiFactory,
        \TeamNeusta\Magedev\Docker\Container\AbstractContainer $container
    ) {
        $this->containerManager = $containerManager;
        $this->imageFactory = $imageFactory;
        $this->imageApiFactory = $imageApiFactory;
        $this->container = $container;
    }

    /**
     * start.
     */
    public function start()
    {
        if (!$this->exists()) {
            $this->build();
        }
        if (!$this->isRunning()) {
            try {
                $this->containerManager->start($this->container->getBuildName());
            } catch (\Http\Client\Common\Exception\ServerErrorException $e) {
                // TODO error handling
                // grap logs from journalctl -u docker.service
                echo 'failed to start '.$this->container->getBuildName().' with '.$e->getMessage()."\n";
            }
        }
    }

    /**
     * stop.
     */
    public function stop()
    {
        $name = $this->container->getBuildName();
        if ($this->isRunning()) {
            $this->containerManager->stop($name);
        }
    }

    /**
     * exists.
     *
     * @return bool
     */
    public function exists()
    {
        $containerName = $this->container->getBuildName();
        $containers = $this->containerManager->findAll(['all' => true]);
        foreach ($containers as $container) {
            foreach ($container->getNames() as $name) {
                if ($name === '/'.$containerName) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * isRunning.
     *
     * @return bool
     */
    public function isRunning()
    {
        $containerName = $this->container->getBuildName();
        // TODO; make this more DRY
        $containers = $this->containerManager->findAll();
        foreach ($containers as $container) {
            foreach ($container->getNames() as $name) {
                if ($name === '/'.$containerName) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * build.
     */
    public function build()
    {
        if (!$this->exists()) {
            $containerConfig = $this->container->getConfig();
            $image = $this->container->getImage();

            if (is_string($image)) {
                $imageName = $image;
                $image = $this->imageFactory->create('ExternImage');
                $image->setBuildName($imageName);
                /* $image = (new \TeamNeusta\Magedev\Docker\Image\ExternImage($this->context)) */
                    /* ->setBuildName($imageName); */
                $this->imageApiFactory->create($image)->pull(); // is an extern image lets pull it
            }
            if (!($image instanceof \TeamNeusta\Magedev\Docker\Image\AbstractImage)) {
                throw new \Exception('image of '.get_class($image).' cannot be build');
            }

            $imageApi = $this->imageApiFactory->create($image);

            if (!$imageApi->exists()) {
                $imageApi->build();
            }

            $containerConfig->setImage($image->getBuildName());
            $containerCreateResult = $this->containerManager->create(
                $containerConfig,
                ['name' => $this->container->getBuildName()]
            );
        }
    }

    /**
     * destroy.
     */
    public function destroy()
    {
        if ($this->exists()) {
            if (!$this->isRunning()) {
                $this->containerManager->remove($this->container->getBuildName());
            }
        }
    }
}
