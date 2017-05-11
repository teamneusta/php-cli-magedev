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

/**
 * Class Config
 */
class Config
{
    /**
     * @var string
     */
    protected $projectName;

    /**
     * @var string
     */
    protected $projectPath;

    /**
     * @var string
     */
    protected $homePath;

    /**
     * @var string
     */
    protected $documentRootPath;

    /**
     * magentoVersion
     *
     * @var string
     */
    protected $magentoVersion;

    /**
     * @var string
     */
    protected $gateway;

    /**
     * @var string
     */
    protected $networkId;

    /**
     * setProjectName
     *
     * @param string $projectName
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
        return $this;
    }

    /**
     * getProjectName
     * @return string
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * setProjectPath
     *
     * @param string $projectPath
     */
    public function setProjectPath($projectPath)
    {
        $this->projectPath = $projectPath;
        return $this;
    }

    /**
     * getProjectPath
     * @return string
     */
    public function getProjectPath()
    {
        return $this->projectPath;
    }

    /**
     * setHomePath
     *
     * @param string $homePath
     */
    public function setHomePath($homePath)
    {
        $this->homePath = $homePath;
    }

    /**
     * getHomePath
     * @return string
     */
    public function getHomePath()
    {
        return $this->homePath;
    }

    /**
     * setDocumentRootPath
     *
     * @param string $rootPath
     */
    public function setDocumentRootPath($rootPath)
    {
        $this->documentRootPath = $rootPath;
    }

    /**
     * getDocumentRootPath
     * @return string
     */
    public function getDocumentRootPath()
    {
        return $this->documentRootPath;
    }

    /**
     * setMagentoVersion
     *
     * @param string $magentoVersion
     */
    public function setMagentoVersion($magentoVersion)
    {
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * getMagentoVersion
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->magentoVersion;
    }

    /**
     * setGateway
     *
     * @param string $gateway
     */
    public function setGateway($gateway)
    {
        $gateway = strstr($gateway, "/", true);
        $this->gateway = $gateway;
    }

    /**
     * getGateway
     * @return string
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * setNetworkId
     *
     * @param string $networkId
     */
    public function setNetworkId($networkId)
    {
        $this->networkId = $networkId;
    }

    /**
     * getNetworkId
     * @return string
     */
    public function getNetworkId()
    {
        return $this->networkId;
    }

}
