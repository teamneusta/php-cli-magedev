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

namespace TeamNeusta\Magedev\Test\Commands\Init;

use \Mockery as m;
use TeamNeusta\Magedev\Commands\Init\NpmCommand;

/**
 * Class: NpmCommandTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class NpmCommandTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testExecute()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('get')->with("source_folder")->andReturn("./");
        $config->shouldReceive('optionExists')->with("proxy")->andReturn(false);
        $config->shouldReceive('getMagentoVersion')->andReturn("2");

        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $shellService = m::mock('\TeamNeusta\Magedev\Services\ShellService');
        $fileHelper = m::mock('\TeamNeusta\Magedev\Helper\FileHelper');
        $fileHelper->shouldReceive('fileExists');
        $dockerService->shouldReceive("execute")->with("bash -c \"[[ ! -f \"/usr/bin/node\" ]] && ln -s /usr/bin/nodejs /usr/bin/node\"", ['user'=>'root']);
        $dockerService->shouldReceive("execute")->with("npm install -g grunt-cli", ['user'=>'root']);
        $dockerService->shouldReceive("execute")->with("npm install", ['user'=>'root']);

        $command = new NpmCommand(
            $config,
            $dockerService,
            $shellService,
            $fileHelper
        );
        $command->execute($input, $output);
    }

    public function testExecuteWithProxySettings()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('get')->with("source_folder")->andReturn("./");
        $config->shouldReceive('get')->with("proxy")->andReturn(['HTTP' => 'http://someproxy.de:8080', 'HTTPS' => 'http://someproxy.de:8080']);

        $config->shouldReceive('optionExists')->with("proxy")->andReturn(true);
        $config->shouldReceive('getMagentoVersion')->andReturn("2");

        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $shellService = m::mock('\TeamNeusta\Magedev\Services\ShellService');
        $fileHelper = m::mock('\TeamNeusta\Magedev\Helper\FileHelper');
        $fileHelper->shouldReceive('fileExists');
        $dockerService->shouldReceive("execute")->with("bash -c \"[[ ! -f \"/usr/bin/node\" ]] && ln -s /usr/bin/nodejs /usr/bin/node\"", ['user'=>'root']);
        $dockerService->shouldReceive("execute")->with("npm config set https-proxy http://someproxy.de:8080 && npm config set proxy http://someproxy.de:8080 && npm install -g grunt-cli", ['user'=>'root']);
        $dockerService->shouldReceive("execute")->with("npm config set https-proxy http://someproxy.de:8080 && npm config set proxy http://someproxy.de:8080 && npm install", ['user'=>'root']);

        $command = new NpmCommand(
            $config,
            $dockerService,
            $shellService,
            $fileHelper
        );
        $command->execute($input, $output);
    }
}
