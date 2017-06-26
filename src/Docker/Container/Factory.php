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
     * @var \TeamNeusta\Magedev\Docker\Image\Factory
     */
    protected $imageFactory;

    /**
     * @var \TeamNeusta\Magedev\Docker\Helper\NameBuilder
     */
    protected $nameBuilder;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config       $config
     * @param \TeamNeusta\Magedev\Docker\Image\Factory $imageFactory
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Docker\Image\Factory $imageFactory,
        \TeamNeusta\Magedev\Docker\Helper\NameBuilder $nameBuilder
    ) {
        $this->config = $config;
        $this->imageFactory = $imageFactory;
        $this->nameBuilder = $nameBuilder;
    }

    public function create($className)
    {
        $className = '\\TeamNeusta\\Magedev\\Docker\\Container\\Repository\\'.$className;

        return new $className(
            $this->config,
            $this->imageFactory,
            $this->nameBuilder
        );
    }
}
