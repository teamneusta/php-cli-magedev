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
use TeamNeusta\Magedev\Commands\Magento\UpgradeCommand;

/**
 * Class: UpgradeCommandTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class UpgradeCommandTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testExecute()
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
        $config->shouldReceive('getMagentoVersion')->andReturn("2");

        $magerunHelper = m::mock('\TeamNeusta\Magedev\Runtime\Helper\MagerunHelper');

        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $dockerService->shouldReceive('execute')->with("bin/magento setup:upgrade");

        $command = m::mock(
            "\TeamNeusta\Magedev\Commands\Magento\UpgradeCommand[getName,getApplication]",
            [$config, $magerunHelper, $dockerService]
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

