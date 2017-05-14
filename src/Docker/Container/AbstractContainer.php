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

use Docker\API\Model\ContainerConfig;
use Docker\API\Model\EndpointSettings;
use Docker\API\Model\HostConfig;
use Docker\API\Model\NetworkCreateConfig;
use Docker\API\Model\NetworkingConfig;
use Docker\API\Model\PortBinding;

/**
 * Class: AbstractContainer
 *
 * @see DockerContainer
 * @abstract
 */
abstract class AbstractContainer
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
     * @var string[]
     */
    protected $binds;

    /**
     * @var \ArrayObject
     */
    protected $mapPorts;

    /**
     * @var Array
     */
    protected $links = [];

    /**
     * __construct
     *
     * @param \TeamNeusta\Magedev\Runtime\Config $config
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Docker\Image\Factory $imageFactory
    ) {
        $this->config = $config;
        $this->imageFactory = $imageFactory;
        $this->mapPorts = new \ArrayObject();
        $this->binds = [];
        $this->links = [];
    }

    /**
     * getName
     * @return containerName
     */
    public abstract function getName();

    /**
     * getBuildName
     * @return project specifc container name
     */
    public function getBuildName()
    {
        return $this->context->buildName(
             $this->getName()
        );
    }

    /**
     * getConfig
     * @return \Docker\API\Model\ContainerConfig
     */
    public function getConfig()
    {
        $config = new ContainerConfig();
        $endpointSettings = new EndpointSettings();
        $endpointSettings->setLinks($this->links);
        $endpointSettings->setNetworkID($this->config->get("network_id"));

        // TODO: make this configurable?
        $networkName = "magedev_default";

        $networkingConfig = new NetworkingConfig();
        $networkingConfig->setEndpointsConfig(new \ArrayObject([$networkName => $endpointSettings]));

        $config->setNetworkingConfig($networkingConfig);
        $hostConfig = new HostConfig();
        $hostConfig->setBinds($this->binds);
        $hostConfig->setPortBindings($this->mapPorts);
        $config->setHostConfig($hostConfig);

        $env = [];
        foreach ($this->config->get('env_vars') as $key => $value) {
            $env[] = $key . "=" . $value;
        }

        $config->setEnv($env);

        return $config;
    }

    /**
     * addLink
     *
     * @param string $link
     */
    public function addLink($link)
    {
        $this->links[] = $link;
    }

    /**
     * forwardPort
     *
     * @param string $srcPort
     * @param string $dstPort
     */
    public function forwardPort($srcPort, $dstPort)
    {
        $hostPortBinding = new PortBinding();
        $hostPortBinding->setHostPort((string)$dstPort);
        $hostPortBinding->setHostIp('0.0.0.0');
        $this->mapPorts[$srcPort . '/tcp'] = [$hostPortBinding];
    }

    /**
     * setBinds
     *
     * @param string[] $binds
     */
    public function setBinds($binds = [])
    {
        $this->binds = $binds;
    }
}
