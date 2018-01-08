<?php
/**
 * This file is part of the teamneusta/php-cli-magedev package.
 *
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @license https://opensource.org/licenses/mit-license BSD-3-Clause License
 */

namespace TeamNeusta\Magedev\Services;

use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Docker\Manager as DockerManager;
use TeamNeusta\Magedev\Docker\Network as NetworkManager;
use TeamNeusta\Magedev\Docker\Container\Factory as ContainerFactory;
use TeamNeusta\Magedev\Docker\Helper\NameBuilder;

/**
 * Class DockerService.
 */
class DockerService
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Services\ShellService
     */
    protected $shell;

    /**
     * @var \TeamNeusta\Magedev\Runtime\Helper\FileHelper
     */
    protected $fileHelper;

    /**
     * @var \TeamNeusta\Magedev\Docker\Manager
     */
    protected $dockerManager;

    /**
     * @var \TeamNeusta\Magedev\Docker\Network
     */
    protected $networkManager;

    /**
     * @var \TeamNeusta\Magedev\Docker\Container\Factory
     */
    protected $containerFactory;

    /**
     * @var \TeamNeusta\Magedev\Docker\Helper\NameBuilder
     */
    protected $nameBuilder;

    /**
     * @var bool
     */
    protected $dockerInited = false;

    /**
     * __construct.
     *
     * @param Config                                        $config
     * @param ConsoleOutput                                 $output
     * @param Shell                                         $shell
     * @param FileHelper                                    $fileHelper
     * @param \TeamNeusta\Magedev\Docker\Manager            $dockerManager
     * @param \TeamNeusta\Magedev\Docker\Network            $networkManager
     * @param \TeamNeusta\Magedev\Docker\Container\Factory  $containerFactory
     * @param \TeamNeusta\Magedev\Docker\Helper\NameBuilder $nameBuilder
     */
    public function __construct(
        Config $config,
        OutputInterface $output,
        ShellService $shell,
        FileHelper $fileHelper,
        DockerManager $dockerManager,
        NetworkManager $networkManager,
        ContainerFactory $containerFactory,
        NameBuilder $nameBuilder
    ) {
        $this->config = $config;
        $this->output = $output;
        $this->shell = $shell;
        $this->fileHelper = $fileHelper;
        $this->dockerManager = $dockerManager;
        $this->networkManager = $networkManager;
        $this->containerFactory = $containerFactory;
        $this->nameBuilder = $nameBuilder;
    }

    protected function initDocker()
    {
        if ($this->dockerInited) {
            return;
        }
        $this->dockerInited = true;

        $this->applyDockerSettingsToConfig();

        $dockerLinks = [];
        $dockerPorts = [];
        $containers = [];

        if (!$this->config->optionExists('docker')) {
            throw new \Exception('no docker config found. check your magedev.json please');
        }

        $dockerConfig = $this->config->get('docker');
        if (array_key_exists('links', $dockerConfig)) {
            $dockerLinks = $dockerConfig['links'];
        }
        if (array_key_exists('ports', $dockerConfig)) {
            $dockerPorts = $dockerConfig['ports'];
        }
        if (array_key_exists('containers', $dockerConfig)) {
            foreach (array_unique($dockerConfig['containers']) as $containerName) {
                $containers[] = $this->containerFactory->create($containerName);
            }
        }

        if (sizeof($containers) == 0) {
            throw new \Exception('no containers found, please check your magedev.json');
        }

        foreach ($containers as $container) {
            // extract name for container out of classname
            // e.g. elasticsearch, main, mysql ...
            $name = strtolower($this->getClassName(get_class($container)));
            if (array_key_exists($name, $dockerPorts)) {
                /* may look like: */
                /* $portMaps = [ */
                /*     "main" => [ */
                /*         80 => 80 */
                /*     ], */
                /*     "mysql" => [ */
                /*         3306 => 3306 */
                /*     ], */
                /*     ... */
                foreach ($dockerPorts[$name] as $srcPort => $dstPort) {
                    $container->forwardPort($srcPort, $dstPort);
                }
            }

            if (array_key_exists($name, $dockerLinks)) {
                /* may look like: */
                /* $links = [ */
                /*     "main" => ["mysql", "redis", "elasticsearch"] */
                /* ]; */
                foreach ($dockerLinks[$name] as $link) {
                    // format: "containerName:alias", whereas containerName
                    // is built dynamically out of projectname
                    $link = $this->nameBuilder->buildName($link).':'.$link;
                    $this->debugOut("Link containers: " . $link);
                    $container->addLink($link);
                }
            }

            $this->dockerManager->addContainer($container);
        }
        $this->addEnv($containers);
    }

    /**
     * getManager.
     */
    public function getManager()
    {
        $this->initDocker();
        return $this->dockerManager;
    }

    /**
     * getConfig
     * @return \TeamNeusta\Magedev\Runtime\Config
     *
     */
    public function getConfig()
    {
        $this->initDocker();
        return $this->config;
    }

    protected function applyDockerSettingsToConfig()
    {
        if (!$this->config->optionExists('project_name')) {
            // fallback to directory name as project name
            $this->config->set('project_name', basename(getcwd()));
        }
        $this->config->set('project_path', getcwd());
        $this->config->set('home_path', $this->fileHelper->expandPath('~'));
        $this->config->set('document_root', '/var/www/html/'.$this->config->get('source_folder'));

        // TODO: make this configurable?
        $networkName = 'magedev_default';
        // make sure this network exists
        if (!$this->networkManager->networkExists($networkName)) {
            $this->networkManager->createNetwork($networkName);
            if (!$this->networkManager->networkExists($networkName)) {
                throw new \Exception('something went wrong while creating network '.$networkName);
            }
        }

        $network = $this->networkManager->getNetworkByName($networkName);
        if (!$network->getId()) {
            throw new \Exception('no id for network '.$network->getName().' found.');
        }
        $this->config->set('network_id', $network->getId());
        $gateway = $this->networkManager->getGatewayForNetwork($network);
        $this->config->set('gateway', $gateway);

        if (empty($this->config->get('gateway'))) {
            throw new \Exception('no gateway ip found');
        }
    }

    protected function addEnv($containers)
    {
        $containerNames = array_map(function($container) {
            return $container->getName();
        }, $containers);
        $envVars = [];
        $envVars['USERID'] = getmyuid();
        $envVars['MYSQL_ROOT'] = 'root';
        $envVars['MYSQL_ROOT_PASSWORD'] = 'root';
        $envVars['MYSQL_USER'] = 'magento';
        $envVars['MYSQL_PASSWORD'] = 'magento';
        $envVars['MYSQL_DATABASE'] = 'magento';

        if ($this->config->optionExists('proxy')) {
            $proxy = $this->config->get('proxy');
            if (array_key_exists('HTTP', $proxy)) {
                $envVars['HTTP_PROXY'] = $proxy['HTTP'];
                $envVars['http_proxy'] = $proxy['HTTP'];
            }
            if (array_key_exists('HTTPS', $proxy)) {
                $envVars['HTTPS_PROXY'] = $proxy['HTTPS'];
                $envVars['https_proxy'] = $proxy['HTTPS'];
            }
            $envVars['HTTPS_PROXY_REQUEST_FULLURI'] = 'false';
            $noProxyHosts = $containerNames;
            $noProxyHosts[] = 'localhost';
            $envVars['no_proxy'] = "'" . implode(",", $noProxyHosts) . "'";
        }
        $this->config->set('env_vars', $envVars);
    }

    /**
     * execute.
     *
     * @param string   $cmd
     * @param string[] $options
     */
    public function execute($cmd, $options = [])
    {
        $this->initDocker();

        $user = 'www-data';
        $containerName = 'main';

        if (array_key_exists('user', $options)) {
            $user = $options['user'];
        }

        if (array_key_exists('container', $options)) {
            $containerName = $options['container'];
        }

        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln('exec '.$user.'@'.$containerName.': '.$cmd);
        }

        // if execution is in main container use source folder as path
        if ($containerName == 'main') {
            $wd = $this->config->get('source_folder');
            $cmd = 'cd '.$wd.' && '.$cmd;
        }
        $cmd = 'bash -c "'.addcslashes($cmd, '"').'"';

        $dockerManager = $this->getManager();

        $container = $dockerManager->findContainer($containerName);

        if (!$container) {
            throw new \Exception('Sorry, container with name '.$containerName.' is not running');
        }

        $containerName = $container->getBuildName();

        if (!$dockerManager->isRunning($containerName)) {
            throw new \Exception('Cannot execute command. Expected to find container named '.$containerName);
        }

        $cmd = 'docker exec --user='.$user.' -it '.$containerName.' '.$cmd;

        $interactive = true;
        if (array_key_exists('interactive', $options)) {
            $interactive = $options['interactive'];
        }

        return $this->shell->execute($cmd, $interactive);
    }

    public function debugOut($str)
    {
        if ($this->output->isVerbose()) {
            $this->output->writeln($str);
        }
    }

    /**
     * getClassName.
     *
     * @param string $classname
     *
     * @return string
     */
    private function getClassName($classname)
    {
        if ($pos = strrpos($classname, '\\')) {
            return substr($classname, $pos + 1);
        }

        return $pos;
    }
}
