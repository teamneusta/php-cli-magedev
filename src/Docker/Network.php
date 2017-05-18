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

use Docker\Manager\NetworkManager;
use Docker\API\Model\NetworkCreateConfig;

/**
 * Class Network.
 */
class Network
{
    /**
     * @var \Docker\Manager\NetworkManager
     */
    protected $networkManager;

    public function __construct(NetworkManager $networkManager = null)
    {
        if (!$networkManager) {
            $networkManager = (new \Docker\Docker())->getNetworkManager();
        }
        $this->networkManager = $networkManager;
    }
    /**
     * networkExists.
     *
     * @param mixed $name
     *
     * @return bool
     */
    public function networkExists($name)
    {
        return $this->getNetworkByName($name) != null;
    }

    /**
     * getNetworkByName.
     *
     * @param string $name
     *
     * @return \Docker\API\Model\Network
     */
    public function getNetworkByName($name)
    {
        $networks = $this->networkManager->findAll();
        foreach ($networks as $network) {
            if ($network->getName() == $name) {
                return $network;
            }
        }

        return;
    }

    /**
     * createNetwork.
     *
     * @param string $name
     */
    public function createNetwork($name)
    {
        $config = new NetworkCreateConfig();
        $config->setName($name);
        $this->networkManager->create($config);
    }

    /**
     * getGatewayForNetwork.
     *
     * @param \Docker\API\Model\Network $network
     */
    public function getGatewayForNetwork(\Docker\API\Model\Network $network)
    {
        $ipam = $network->getIPAM();
        if ($ipam && $ipam instanceof \Docker\API\Model\IPAM) {
            $config = $ipam->getConfig();
            if (is_array($config)) {
                $config = array_values($config)[0];
            }
            if ($config && $config instanceof \Docker\API\Model\IPAMConfig) {
                return $config->getGateway();
            }
        }
        throw new \Exception('Gateway for Network '.$network->getName().' could not be found');
    }
}
