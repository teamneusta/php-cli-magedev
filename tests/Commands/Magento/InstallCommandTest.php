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

namespace TeamNeusta\Magedev\Test\Commands\Magento;

use \Mockery as m;
use TeamNeusta\Magedev\Commands\Magento\InstallCommand;

/**
 * Class: InstallCommandTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class InstallCommandTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testExecute()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $command = m::mock('\Symfony\Component\Console\Command\Command')->shouldAllowMockingProtectedMethods();
        $command->shouldReceive('execute');

        $application = m::mock('\Symfony\Component\Console\Application');
        $application->shouldReceive('find')->with('init:composer')->andReturn($command);
        $application->shouldReceive('find')->with('init:npm')->andReturn($command);
        $application->shouldReceive('find')->with('init:permissions')->andReturn($command);
        $application->shouldReceive('find')->with('config:reset')->andReturn($command);
        $application->shouldReceive('find')->with('init:add-host-entry')->andReturn($command);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('getMagentoVersion')->andReturn("2");
        $config->shouldReceive('get')->with("domain")->andReturn("magento2.local");

        $domain = "magento2.local";
        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $dockerService->shouldReceive('execute')->with("bin/magento setup:install --db-host=mysql --db-name=magento --db-user=magento --db-password=magento --admin-user=admin --admin-password=admin123 --admin-email=admin@localhost.de --admin-firstname=admin --admin-lastname=admin --backend-frontname=admin --base-url=http://".$domain."/");

        $command = m::mock(
            "\TeamNeusta\Magedev\Commands\Magento\InstallCommand[getName,getApplication]",
            [$config, $dockerService]
        )->makePartial();
        $command->shouldReceive('setName');
        $command->shouldReceive('getApplication')->andReturn($application);

        $command->execute($input, $output);
    }

    public function testExecuteShouldUseMagerunOnMagento1()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $command = m::mock('\Symfony\Component\Console\Command\Command')->shouldAllowMockingProtectedMethods();
        $command->shouldReceive('execute');

        $application = m::mock('\Symfony\Component\Console\Application');
        $application->shouldReceive('find')->with('magento:cache:clean')->andReturn($command);
        $application->shouldReceive('find')->with('magento:refresh')->andReturn($command);
        $application->shouldReceive('find')->with('init:permissions')->andReturn($command);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('getMagentoVersion')->andReturn("1");

        $magerunHelper = m::mock('\TeamNeusta\Magedev\Runtime\Helper\MagerunHelper');
        $magerunHelper->shouldReceive('magerunCommand')->with("sys:setup:run");

        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');

        $command = m::mock(
            "\TeamNeusta\Magedev\Commands\Magento\UpgradeCommand[getName,getApplication]",
            [$config, $magerunHelper, $dockerService]
        )->makePartial();
        $command->shouldReceive('setName');
        $command->shouldReceive('getApplication')->andReturn($application);

        $command->execute($input, $output);

    }
}

