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
use TeamNeusta\Magedev\Services\ShellService;

/**
 * Class DockerService
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
     * __construct
     *
     * @param Config $config
     * @param ConsoleOutput $output
     * @param Shell $shell
     * @param FileHelper $fileHelper
     */
    public function __construct(
        Config $config,
        OutputInterface $output,
        ShellService $shell,
        FileHelper $fileHelper
    ) {
        $this->config = $config;
        $this->output = $output;
        $this->shell = $shell;
        $this->fileHelper = $fileHelper;
    }

    /**
     * getManager
     */
    public function getManager()
    {
        $dockerManager = new \TeamNeusta\Magedev\Docker\Manager();
        $context = $this->getContext();

        $context->addEnv("USERID", getmyuid());
        $context->addEnv("MYSQL_ROOT", "root");
        $context->addEnv("MYSQL_ROOT_PASSWORD", "root");
        $context->addEnv("MYSQL_USER", "magento");
        $context->addEnv("MYSQL_PASSWORD", "magento");
        $context->addEnv("MYSQL_DATABASE", "magento");

        if ($this->config->optionExists("proxy")) {
            $proxy = $this->config->get("proxy");
            if (array_key_exists("HTTP", $proxy)) {
                $context->addEnv("HTTP_PROXY", $proxy["HTTP"]);
                $context->addEnv("http_proxy", $proxy["HTTP"]);
            }
            if (array_key_exists("HTTPS", $proxy)) {
                $context->addEnv("HTTPS_PROXY", $proxy["HTTPS"]);
                $context->addEnv("https_proxy", $proxy["HTTPS"]);
            }
            $context->addEnv("HTTPS_PROXY_REQUEST_FULLURI", "false");
            $context->addEnv("no_proxy", "'localhost,elasticsearch,httpd,mysql'");
        }

        $dockerLinks = [];
        $dockerPorts = [];

        if ($this->config->optionExists("docker")) {
            $dockerConfig = $this->config->get("docker");
            if (array_key_exists("links", $dockerConfig)) {
                $dockerLinks = $dockerConfig["links"];
            }
            if (array_key_exists("ports", $dockerConfig)) {
                $dockerPorts = $dockerConfig["ports"];
            }
        }

        $containers = [
            new \TeamNeusta\Magedev\Docker\Container\Repository\ElasticSearch($context),
            new \TeamNeusta\Magedev\Docker\Container\Repository\Mailcatcher($context),
            new \TeamNeusta\Magedev\Docker\Container\Repository\Main($context),
            new \TeamNeusta\Magedev\Docker\Container\Repository\Mysql($context),
            new \TeamNeusta\Magedev\Docker\Container\Repository\Redis($context),
            new \TeamNeusta\Magedev\Docker\Container\Repository\Varnish($context)
        ];
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
                    $container->addLink($context->buildName($link) . ":" . $link);
                }
            }
            $dockerManager->containers()->add($name, $container);
        }

        return $dockerManager;
    }

    /**
     * getContext
     * @return \TeamNeusta\Magedev\Docker\Config
     */
    public function getContext()
    {
        $dockerConfig = new \TeamNeusta\Magedev\Docker\Config();
        $dockerConfig->setProjectName(basename(getcwd()));
        $dockerConfig->setProjectPath(getcwd());
        $dockerConfig->setHomePath($this->fileHelper->expandPath("~"));
        $dockerConfig->setDocumentRootPath("/var/www/html/" . $this->config->get("source_folder"));
        $dockerConfig->setMagentoVersion($this->config->getMagentoVersion());


        $networkManager = new \TeamNeusta\Magedev\Docker\Network();
        // TODO: make this configurable?
        $networkName = "magedev_default";
        // make sure this network exists
        if (!$networkManager->networkExists($networkName)) {
            $networkManager->createNetwork($networkName);
            if (!$networkManager->networkExists($networkName)) {
                throw new \Exception("something went wrong while creating network " . $networkName);
            }
        }

        $network = $networkManager->getNetworkByName($networkName);
        if (!$network->getId()) {
            throw new \Exception("no id for network " . $network->getName() . " found.");
        }
        $dockerConfig->setNetworkId($network->getId());
        $gateway = $networkManager->getGatewayForNetwork($network);
        $dockerConfig->setGateway($gateway);

        if (empty($dockerConfig->getGateway())) {
            throw new \Exception("no gateway ip found");
        }

        $context = new \TeamNeusta\Magedev\Docker\Context($dockerConfig, $this->fileHelper);

        return $context;
    }

    /**
     * execute
     *
     * @param string $cmd
     * @param string[] $options
     */
    public function execute($cmd, $options = [])
    {
        $user = "www-data";
        $containerName = "main";

        if (array_key_exists('user', $options)) {
            $user = $options['user'];
        }

        if (array_key_exists('container', $options)) {
            $containerName = $options['container'];
        }

        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln("exec " . $user . "@" . $containerName . ": " . $cmd);
        }

        // if execution is in main container use source folder as path
        if ($containerName == "main") {
            $wd = $this->config->get("source_folder");
            $cmd = "cd ".$wd." && ".$cmd;
        }
        $cmd = "bash -c \"".addcslashes($cmd, '"')."\"";

        $dockerManager = $this->getManager();

        $container = $dockerManager
            ->containers()
            ->find($containerName);

        if (!$container) {
            throw new \Exception("Sorry, container with name " . $containerName . " is not running");
        }

        $containerName = $container->getBuildName();

        if (!$container->isRunning()) {
            throw new \Exception("Cannot execute command. Expected to find container named ".$containerName);
        }

        $cmd = "docker exec --user=".$user." -it " . $containerName . " " . $cmd;

        $interactive = true;
        if (array_key_exists('interactive', $options)) {
            $interactive = $options['interactive'];
        }
        return $this->shell->execute($cmd, $interactive);
    }

    /**
     * getClassName
     *
     * @param string $classname
     * @return string
     */
    private function getClassName($classname)
    {
        if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
        return $pos;
    }
}
