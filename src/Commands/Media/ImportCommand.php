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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Services\ShellService;

/**
 * Class: ImportCommand.
 *
 * @see AbstractCommand
 */
class ImportCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Services\ShellService
     */
    protected $shellService;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config        $config
     * @param \TeamNeusta\Magedev\Services\ShellService $shellService
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Services\ShellService $shellService
    ) {
        $this->config = $config;
        $this->shellService = $shellService;
        parent::__construct();
    }

    /**
     * configure.
     */
    protected function configure()
    {
        $this->setName('media:import');
        $this->setDescription('imports media tar archive');
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $magentoVersion = $this->config->getMagentoVersion();

        if ($this->config->optionExists('media_archive')) {
            $mediaArchive = $this->config->get('media_archive');

            $possibleExtensions = ['tar', 'tar.gz', 'zip'];

            $extension = $this->fileExtension($mediaArchive, $possibleExtensions);

            $destinationPath = '.';

            if ($magentoVersion == '2') {
                $destinationPath = 'pub';
            }

            // escape whitespaces for command
            $mediaArchive = str_replace(' ', '\\ ', $mediaArchive);

            // TODO: remove v argument if output is not verbose
            if ($extension == 'tar') {
                $cmd = 'tar -xvf '.$mediaArchive.' -C '.$destinationPath;
            }

            if ($extension == 'tar.gz') {
                $cmd = 'tar -xvzf '.$mediaArchive.' -C '.$destinationPath;
            }

            if ($extension == 'zip') {
                $cmd = 'unzip '.$mediaArchive.' -d '.$destinationPath;
            }

            if (empty($cmd)) {
                throw new \Exception('unkown archive extension for '.$mediaArchive);
            }

            $this->shellService
                ->wd($this->config->get('source_folder'))
                ->bash($cmd);
        }
    }

    /**
     * fileExtension.
     *
     * @param string   $path
     * @param string[] $extensions
     */
    public function fileExtension($path, $extensions = [])
    {
        $output_array = [];
        preg_match("/^.*\.(".implode('|', $extensions).')$/', $path, $output_array);

        return end($output_array);
    }
}
