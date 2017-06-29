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

            return $this->array_merge_recursive_distinct($this->array_merge_recursive_distinct($defaultConfig, $homeConfig), $projectConfig);
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

    public function set($key, $value)
    {
        $this->configData[$key] = $value;
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

    /**
     * Copy from http://www.php.net/manual/en/function.array-merge-recursive.php#92195
     *
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     */
    public function array_merge_recursive_distinct(array $array1, array $array2)
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value)
        {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key]))
            {
                $merged[$key] = $this->array_merge_recursive_distinct($merged[$key], $value);
            }
            else
            {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}
