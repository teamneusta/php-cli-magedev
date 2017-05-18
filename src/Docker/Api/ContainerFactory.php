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

use Docker\Manager\ContainerManager;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;

/**
 * Class ContainerFactory.
 */
class ContainerFactory
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
        \TeamNeusta\Magedev\Docker\Api\ImageFactory $imageApiFactory
    ) {
        $this->containerManager = $containerManager;
        $this->imageFactory = $imageFactory;
        $this->imageApiFactory = $imageApiFactory;
    }

    /**
     * create.
     *
     * @param \TeamNeusta\Magedev\Docker\Container\AbstractContainer $container
     */
    public function create(\TeamNeusta\Magedev\Docker\Container\AbstractContainer $container)
    {
        return new Container(
            $this->containerManager,
            $this->imageFactory,
            $this->imageApiFactory,
            $container
        );
    }
}
