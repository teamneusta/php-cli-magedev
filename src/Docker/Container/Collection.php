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
 * Class: Collection
 */
class Collection
{
    /**
     * @var DockerContainer[]
     */
    protected $containers = [];

    /**
     * add
     *
     * @param string $name
     * @param DockerContainer $container
     */
    public function add($name, DockerContainer $container)
    {
        $this->containers[$name] = $container;
    }

    /**
     * find
     *
     * @param string $name
     * @return DockerContainer
     */
    public function find($name)
    {
        return $this->containers[$name];
    }

    /**
     * eachContainer
     *
     * @param callable $callback
     */
    public function eachContainer(callable $callback)
    {
        foreach ($this->containers as $name => $container) {
            $callback($container);
        }
    }
}
