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

namespace TeamNeusta\Magedev\Commands\Media;

use TeamNeusta\Magedev\Commands\AbstractCommand;

/**
 * Class: ImportCommand
 *
 * @see AbstractCommand
 */
class ImportCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName('media:import');
        $this->setDescription('imports media tar archive');

        $this->onExecute(function ($runtime) {
            $config = $runtime->getConfig();
            $fileHelper = $runtime->getHelper('FileHelper');
            $magentoVersion = $config->getMagentoVersion();

            if ($config->optionExists('media_archive')) {
                $mediaArchive = $config->get("media_archive");

                $possibleExtensions = ["tar", "tar.gz", "zip"];

                $extension = $this->fileExtension($mediaArchive, $possibleExtensions);

                $destinationPath = ".";

                if ($magentoVersion == "2") {
                    $destinationPath = "pub";
                }

                // escape whitespaces for command
                $mediaArchive = str_replace(" ", "\\ ", $mediaArchive);

                // TODO: remove v argument if output is not verbose
                if ($extension == "tar") {
                    $cmd = "tar -xvf ".$mediaArchive." -C ".$destinationPath;
                }

                if ($extension == "tar.gz") {
                    $cmd = "tar -xvzf ".$mediaArchive." -C ".$destinationPath;
                }

                if ($extension == "zip") {
                    $cmd = "unzip ".$mediaArchive." -d ".$destinationPath;
                }

                if (empty($cmd)) {
                    throw new \Exception("unkown archive extension for ".$mediaArchive);
                }

                $runtime
                    ->getShell()
                    ->wd($config->get("source_folder"))
                    ->bash($cmd);
            }
        });
    }

    /**
     * fileExtension
     *
     * @param string $path
     * @param string[] $extensions
     */
    public function fileExtension($path, $extensions = [])
    {
        $output_array = [];
        preg_match("/^.*\.(".implode('|', $extensions).")$/", $path, $output_array);
        return end($output_array);
    }
}
