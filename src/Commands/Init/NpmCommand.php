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

namespace TeamNeusta\Magedev\Commands\Init;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Services\DockerService;
use TeamNeusta\Magedev\Services\ShellService;

/**
 * Class: NpmCommand
 *
 * @see AbstractCommand
 */
class NpmCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Services\DockerService
     */
    protected $dockerService;

    /**
     * @var \TeamNeusta\Magedev\Services\ShellService
     */
    protected $shellService;

    /**
     * __construct
     *
     * @param \TeamNeusta\Magedev\Runtime\Config $config
     * @param \TeamNeusta\Magedev\Services\DockerService $dockerService
     * @param \TeamNeusta\Magedev\Services\ShellService $shellService
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Services\DockerService $dockerService,
        \TeamNeusta\Magedev\Services\ShellService $shellService
    ) {
        $this->config = $config;
        $this->dockerService = $dockerService;
        $this->shellService = $shellService;
        parent::__construct();
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this->setName('init:npm');
        $this->setDescription('runs npm:install');
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $magentoVersion = $this->config->getMagentoVersion();
        $sourceFolder = $this->config->get("source_folder");
        // npm only used for magento2
        if ($magentoVersion == "2") {
            try {
                $this->dockerService->execute(
                    "bash -c \"[[ ! -f \"/usr/bin/node\" ]] && ln -s /usr/bin/nodejs /usr/bin/node\"",
                    [
                      'user' => 'root'
                    ]
                );
            } catch (\Exception $e) {
                // TODO: handle exception more precisly here
                // this command may exit with return code != 0
                // which aborts setup
            }

            // avoid ENOENT, open '/var/www/html/package.json' error
            if (!file_exists($sourceFolder . "/package.json") && file_exists($sourceFolder . "package.json.sample")) {
                $this->shellService->bash("cp ".$sourceFolder."/package.json.sample ".$sourceFolder."/package.json");
            }
            $this->execNpmCommand("npm install -g grunt-cli");
            $this->execNpmCommand("npm install");
        }
    }

    /**
     * execNpmCommand
     *
     * @param string $cmd
     */
    public function execNpmCommand($cmd) {
        $useProxy = $this->config->optionExists("proxy");
        if ($useProxy) {
            $proxy = $this->config->get("proxy");
            if (array_key_exists("HTTP", $proxy)) {
                $cmd = "npm config set proxy " . $proxy["HTTP"] . " && " . $cmd;
            }
            if (array_key_exists("HTTPS", $proxy)) {
                $cmd = "npm config set https-proxy " . $proxy["HTTP"] . " && " . $cmd;
            }
        }
        $this->dockerService->execute(
            $cmd,
            [
                'user' => 'root'
            ]
        );
    }
}
