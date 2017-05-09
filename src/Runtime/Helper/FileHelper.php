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

namespace TeamNeusta\Magedev\Runtime\Helper;

/**
 * Class FileHelper
 */
class FileHelper
{
    /**
     * findPath
     *
     * @param string $relativePath
     * @return string
     */
    public function findPath($relativePath)
    {
        $searchLocations = [
            getcwd() . '/.magedev/',
            '~/.magedev/',
            __DIR__.'/../../../'];

        foreach ($searchLocations as $basePath) {
            $fullPath = $this->expandPath($basePath . $relativePath);
            if (file_exists($fullPath) || is_dir($fullPath)) {
                return $fullPath;
            }
        }

        throw new \Exception("File " . $relativePath . " could not be resolved. Searched in " . print_r($searchLocations, true));
    }

    /**
     * expandPath
     *
     * file_exist cannot handle short paths like ~ for home folders
     *
     * @param string $path
     * @return string
     */
    public function expandPath($path) {
        return str_replace("~", getenv("HOME"), $path);
    }

    /**
     * read
     *
     * @param string $relativePath
     * @return string
     */
    public function read($relativePath)
    {
        try {
            $srcPath = $this->findPath($relativePath);
            if (file_exists($srcPath)) {
                return file_get_contents($srcPath);
            }
        } catch (\Exception $e) {
            // doesnt matter, fallback to standard read
        }
        if (file_exists($relativePath)) {
            return file_get_contents($relativePath);
        }
        /* Phar::mount('etc/quicklog.ini', 'phar://' . __FILE__ . '/etc/default_quicklog.ini'); */
        throw new \Exception("File ".$relativePath . " could not be found");
    }

    /**
     * fileExists
     *
     * @param string $path
     * @return bool
     */
    public function fileExists($path)
    {
        return file_exists($path);
    }
}
