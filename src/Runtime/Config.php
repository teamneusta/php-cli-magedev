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

namespace TeamNeusta\Magedev\Runtime;

use Symfony\Component\Console\Input\InputInterface;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;

/**
 * Class Config.
 */
class Config
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var FileHelper
     */
    protected $fileHelper;

    /**
     * @var array
     */
    protected $configData;

    /**
     * isLoaded.
     *
     * @var bool
     */
    protected $isLoaded;

    /**
     * __construct.
     *
     * @param Runtime $runtime
     */
    public function __construct(
        InputInterface $input,
        FileHelper $fileHelper
    ) {
        $this->input = $input;
        $this->fileHelper = $fileHelper;
        $this->configData = [];
    }

    /**
     * load.
     */
    public function load()
    {
        $this->configData = $this->loadConfiguration();
        $this->isLoaded = true;
    }

    /**
     * loadConfiguration.
     *
     * @param FileHelper $fileHelper
     */
    protected function loadConfiguration()
    {
        $projectConfigFile = getcwd().'/magedev.json';
        $defaultConfigFile = $this->fileHelper->findPath('var/config/magedev.json');

        if ($this->fileHelper->fileExists($projectConfigFile)) {
            $projectConfig = $this->loadConfigFile($projectConfigFile);
            $defaultConfig = $this->loadConfigFile($defaultConfigFile);
            $homeConfig = [];

            $homeConfigFile = $this->fileHelper->expandPath('~').'/.magedev.json';
            if ($this->fileHelper->fileExists($homeConfigFile)) {
                $homeConfig = $this->loadConfigFile($homeConfigFile);
            }

            return array_merge(array_merge($defaultConfig, $homeConfig), $projectConfig);
        } else {
            throw new \Exception('it seems this is not a magento project I can handle: '.$projectConfigFile.' file was not found');
        }
    }

    /**
     * loadConfigFile.
     *
     * @param string $path
     */
    protected function loadConfigFile($path)
    {
        if (!$this->fileHelper->fileExists($path)) {
            throw new \Exception('File '.$path.' not found');
        }
        $data = json_decode($this->fileHelper->read($path), true);
        if (json_last_error()) {
            throw new \Exception('Parse error in '.$path.': '.json_last_error_msg());
        }
        if (!is_array($data)) {
            throw new \Exception('Parse error in '.$path.': '.json_last_error_msg());
        }

        return $data;
    }

    /**
     * get.
     *
     * @param string $key
     *
     * @return string
     */
    public function get($key)
    {
        if (!$this->isLoaded) {
            $this->load();
        }

        $value = null;

        if ($this->input) {
            try {
                $value = $this->input->getArgument($key);
            } catch (\Symfony\Component\Console\Exception\InvalidArgumentException $e) {
            }
        }

        if ($value) {
            return $value;
        }

        if (!isset($this->configData[$key])) {
            throw new \Exception($key.' not found in config');
        }
        $value = $this->configData[$key];

        return $value;
    }

    public function set($key, $value, $replace = true)
    {
        if ($replace === true || isset($this->configData[$key]) === false) {
            $this->configData[$key] = $value;
        }
    }

    /**
     * optionExists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function optionExists($key)
    {
        try {
            $value = $this->get($key);

            return $value != '';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * getMagentoVersion.
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        $version = $this->get('magento_version');
        if (!in_array($version, ['1', '2'])) {
            throw new \Exception('supplied magento version '.$version.' not available');
        }

        return $version;
    }
}
