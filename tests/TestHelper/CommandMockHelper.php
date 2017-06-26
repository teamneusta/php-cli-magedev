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

namespace TeamNeusta\Magedev\Test\TestHelper;

use TeamNeusta\Magedev\Runtime\Runtime;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\System\Docker;
use TeamNeusta\Magedev\Runtime\System\Shell;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Docker\Manager as DockerManager;

/**
 * Class: CommandMockHelper.
 *
 * @see \PHPUnit_Framework_TestCase
 */
class CommandMockHelper extends \PHPUnit_Framework_TestCase
{
    protected $magedevConfig;

    protected $runtimeMock;

    protected $configMock;

    protected $dockerManagerMock;

    protected $dockerMock;

    protected $shellMock;

    protected $fileHelperMock;

    public function __construct($magedevConfig = null)
    {
        if ($magedevConfig == null) {
            $magedevConfig = [
                'magento_version' => '2',
                'source_folder' => 'Source/',
                'domain' => 'test.domain.de',
            ];
        }
        $this->magedevConfig = $magedevConfig;
    }

    public function getConfig()
    {
        if ($this->configMock == null) {
            $this->configMock = $this->getMockBuilder(Config::class)
                ->disableOriginalConstructor()
                ->setMethods([
                    'loadConfiguration',
                ])
                ->getMock();
            $this->configMock->method('loadConfiguration')->willReturn($this->magedevConfig);
            $this->configMock->load();
        }

        return $this->configMock;
    }

    public function getRuntime()
    {
        if ($this->runtimeMock == null) {
            $output = $this->getMockForAbstractClass('Symfony\Component\Console\Output\ConsoleOutput');
            $input = $this->getMockForAbstractClass('Symfony\Component\Console\Input\InputInterface');
            $questionHelper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
                ->disableOriginalConstructor()
                ->setMethods([
                    'getHelper',
                ])
                ->getMock();
            $this->runtimeMock = $this->getMockBuilder(Runtime::class)
                ->setConstructorArgs([$input, $output, $questionHelper])
                ->setMethods([
                    'loadPlugins',
                    'getConfig',
                    'getDocker',
                    'getHelper',
                ])
                ->getMock();
            $this->runtimeMock->method('getDocker')->willReturn($this->getDocker());
            $this->runtimeMock->method('getConfig')->willReturn($this->getConfig());
            $this->runtimeMock->method('getHelper')->willReturnCallback(
                function ($name) {
                    if ($name == 'FileHelper') {
                        return $this->getFileHelper();
                    }
                    $class = "\TeamNeusta\Magedev\Runtime\Helper\\".$name;
                    if (!class_exists($class)) {
                        throw new \Exception('Requested helper '.$name.' was not found');
                    }

                    return new $class($this->runtimeMock);
                }
            );
        }

        return $this->runtimeMock;
    }

    public function getShell()
    {
        if ($this->shellMock == null) {
            $output = $this->getMockForAbstractClass('Symfony\Component\Console\Output\ConsoleOutput');
            $this->shellMock = $this->getMockBuilder(Shell::class)
                ->setConstructorArgs([
                    $output,
                ])
                ->setMethods([
                    'nativeExecute',
                ])
                ->getMock();
        }

        return $this->shellMock;
    }

    public function getFileHelper()
    {
        if ($this->fileHelperMock == null) {
            $this->fileHelperMock = $this->getMockBuilder(FileHelper::class)
                ->disableOriginalConstructor()
                ->setMethods([
                    'fileExists',
                ])
                ->getMock();
        }

        return $this->fileHelperMock;
    }

    public function getContainerMock()
    {
        $container = $this->getMockBuilder('\TeamNeusta\Magedev\Docker\Container\Repository\Main')
           ->setConstructorArgs([
              $this->getDocker()->getContext(),
           ])
           ->setMethods([
               'isRunning',
           ])
           ->getMock();
        $container->method('isRunning')->willReturn(true);

        return $container;
    }

    public function getDockerManager()
    {
        if ($this->dockerManagerMock == null) {
            $dockerApiMock = $this->getMockBuilder('\Docker\Docker')
                ->disableOriginalConstructor()
                ->setMethods([
                    'getContainerManager',
                    'getImageManager',
                ])
                ->getMock();
            $this->dockerManagerMock = $this->getMockBuilder(DockerManager::class)
                ->setConstructorArgs([
                    $dockerApiMock,
                ])
                ->setMethods([
                    'startContainers',
                    'stopContainers',
                    'rebuildContainers',
                ])
                ->getMock();
            $this->dockerManagerMock->containers()->add('main', $this->getContainerMock());
        }

        return $this->dockerManagerMock;
    }

    public function getDocker()
    {
        if ($this->dockerMock == null) {
            $output = $this->getMockForAbstractClass('Symfony\Component\Console\Output\ConsoleOutput');
            $this->dockerMock = $this->getMockBuilder(Docker::class)
                ->setConstructorArgs([
                    $this->getConfig(),
                    $output,
                    $this->getShell(),
                    $this->getFileHelper(),
                ])
                ->setMethods([
                    'getManager',
                ])
                ->getMock();
            $this->dockerMock->method('getManager')->willReturn($this->getDockerManager());
        }

        return $this->dockerMock;
    }

    public function getCommand($class)
    {
        $command = $this->getMockBuilder($class)
            ->setMethods([
                'createRuntime',
            ])
            ->getMock();
        $command->method('createRuntime')->willReturn($this->getRuntime());

        return $command;
    }

    public function executeCommand($command)
    {
        $output = $this->getMockForAbstractClass('Symfony\Component\Console\Output\OutputInterface');
        $input = $this->getMockForAbstractClass('Symfony\Component\Console\Input\InputInterface');
        $command->execute($input, $output);
    }
}
