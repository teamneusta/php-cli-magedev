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

namespace TeamNeusta\Magedev\Docker\Image;

/**
 * Class: Factory.
 *
 * @see DockerImage
 */
class Factory
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
     * @var \TeamNeusta\Magedev\Docker\Api\ImageFactory
     */
    protected $imageApiFactory;

    /**
     * @var \TeamNeusta\Magedev\Docker\Helper\NameBuilder
     */
    protected $nameBuilder;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config            $config
     * @param \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper,
        \TeamNeusta\Magedev\Docker\Api\ImageFactory $imageApiFactory,
        \TeamNeusta\Magedev\Docker\Helper\NameBuilder $nameBuilder
    ) {
        $this->config = $config;
        $this->fileHelper = $fileHelper;
        $this->imageApiFactory = $imageApiFactory;
        $this->nameBuilder = $nameBuilder;
    }

    public function create($className)
    {
        $className = '\\TeamNeusta\\Magedev\\Docker\\Image\\Repository\\'.$className;
        $contextBuilder = new \Docker\Context\ContextBuilder();

        return new $className(
            $this->config,
            $this,
            $this->fileHelper,
            $contextBuilder,
            $this->imageApiFactory,
            $this->nameBuilder
        );
    }
}
