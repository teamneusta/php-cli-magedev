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

namespace TeamNeusta\Magedev\Docker\Container;

use Docker\Docker;

/**
 * Class DockerContainer
 */
abstract class DockerContainer
{
    /**
     * @var \TeamNeusta\Magedev\Docker\Context
     */
    protected $context;

    /**
     * __construct
     *
     * @param \TeamNeusta\Magedev\Docker\Context $context
     */
    public function __construct(
        \TeamNeusta\Magedev\Docker\Context $context
    ) {
        $this->context = $context;
    }

    /**
     * getBuildName
     * @return string
     */
    public abstract function getBuildName();

    /**
     * getConfig
     * @return \Docker\API\Model\ContainerConfig
     */
    public abstract function getConfig();

    /**
     * getImage
     * @return TeamNeusta\Magedev\Docker\Container | string
     */
    public abstract function getImage();

    /**
     * start
     */
    public function start()
    {
        if (!$this->exists()) {
            $this->build();
        }
        if (!$this->isRunning()) {
            try {
                $this->context->getContainerManager()->start($this->getBuildName());
            } catch (\Http\Client\Common\Exception\ServerErrorException $e) {
                // TODO error handling
                // grap logs from journalctl -u docker.service
                echo "failed to start " . $this->getBuildName();
            }
        }
    }

    /**
     * stop
     */
    public function stop()
    {
        $name = $this->getBuildName();
        if ($this->isRunning()) {
            $this->context->getContainerManager()->stop($name);
        }
    }

    /**
     * exists
     * @return bool
     */
    public function exists()
    {
        $containerName = $this->getBuildName();
        $containers = $this->context->getContainerManager()->findAll(['all' => true]);
        foreach ($containers as $container) {
            foreach ($container->getNames() as $name) {
                if ($name === "/" . $containerName) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * isRunning
     * @return bool
     */
    public function isRunning()
    {
        $containerName = $this->getBuildName();
        // TODO; make this more DRY
        $containers = $this->context->getContainerManager()->findAll();
        foreach ($containers as $container) {
            foreach ($container->getNames() as $name) {
                if ($name === "/" . $containerName) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * build
     */
    public function build()
    {
        if (!$this->exists()) {
            $containerConfig = $this->getConfig();
            $image = $this->getImage();

            if (is_string($image)) {
                $imageName = $image;
                $image = (new \TeamNeusta\Magedev\Docker\Image\ExternImage($this->context))
                    ->setBuildName($imageName);
                $image->pull(); // is an extern image lets pull it
            }
            if (!($image instanceof \TeamNeusta\Magedev\Docker\Image\DockerImage)) {
                throw new Exception ("image of " . get_class($image) . " cannot be build");
            }

            if (!$image->exists()) {
                $image->build();
            }

            $containerConfig->setImage($image->getBuildName());

            $containerCreateResult = $this->context->getContainerManager()->create(
                $containerConfig,
                ['name' => $this->getBuildName()]
            );
        }
    }

    /**
     * destroy
     */
    public function destroy()
    {
        if ($this->exists()) {
            if (!$this->isRunning()) {
                $this->context->getContainerManager()->remove($this->getBuildName());
            }
        }
    }
}
