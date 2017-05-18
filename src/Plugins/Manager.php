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

namespace TeamNeusta\Magedev\Plugins;

use Symfony\Component\EventDispatcher\EventDispatcher;
use TeamNeusta\Magedev\Runtime\Config;

/**
 * Class Manager.
 */
class Manager
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    /**
     * __construct.
     *
     * @param Config          $config
     * @param EventDispatcher $dispatcher
     */
    public function __construct(
        Config $config,
        EventDispatcher $dispatcher
    ) {
        $this->config = $config;
        $this->dispatcher = $dispatcher;
    }

    /**
     * loadPlugins.
     *
     * @param \Pimple\Container $c
     */
    public function loadPlugins(\Pimple\Container $c)
    {
        // TODO: how to make this more generic?
        if ($this->config->optionExists('plugins')) {
            $plugins = $this->config->get('plugins');
            /* $plugins = ["StopServices", "MountSharefolder"]; */
            foreach ($plugins as $plugin) {
                $class = "\TeamNeusta\Magedev\Plugins\Neusta\\".$plugin;
                if (!class_exists($class)) {
                    throw new \Exception('Plugin '.$plugin.' could not be found.');
                }
                new $class($c);
            }
        }
    }

    /**
     * getDispatcher.
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
}
