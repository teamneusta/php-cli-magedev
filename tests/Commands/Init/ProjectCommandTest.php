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
use TeamNeusta\Magedev\Commands\Init\ProjectCommand;

/**
 * Class: ProjectCommandTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class ProjectCommandTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testExecute()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $command = m::mock('\Symfony\Component\Console\Command\Command')->shouldAllowMockingProtectedMethods();
        $command->shouldReceive('execute');

        $application = m::mock('\Symfony\Component\Console\Application');
        $application->shouldReceive('find')->with('init:permissions')->andReturn($command);
        $application->shouldReceive('find')->with('init:composer')->andReturn($command);
        $application->shouldReceive('find')->with('init:npm')->andReturn($command);
        $application->shouldReceive('find')->with('magento:install-magerun')->andReturn($command);
        $application->shouldReceive('find')->with('magento:align-config')->andReturn($command);
        $application->shouldReceive('find')->with('db:import')->andReturn($command);
        $application->shouldReceive('find')->with('media:import')->andReturn($command);
        $application->shouldReceive('find')->with('magento:set-base-url')->andReturn($command);
        $application->shouldReceive('find')->with('magento:upgrade')->andReturn($command);
        $application->shouldReceive('find')->with('magento:admin:default')->andReturn($command);
        $application->shouldReceive('find')->with('magento:customer:default')->andReturn($command);
        $application->shouldReceive('find')->with('init:permissions')->andReturn($command);
        $application->shouldReceive('find')->with('config:reset')->andReturn($command);
        $application->shouldReceive('find')->with('magento:refresh')->andReturn($command);
        $application->shouldReceive('find')->with('magento:cache:clean')->andReturn($command);
        $application->shouldReceive('find')->with('magento:reindex')->andReturn($command);
        $application->shouldReceive('find')->with('init:add-host-entry')->andReturn($command);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('optionExists')->with("domain")->andReturn(true);
        $config->shouldReceive('get')->with("domain")->andReturn("magento2.local");

        $command = m::mock("\TeamNeusta\Magedev\Commands\Init\ProjectCommand[getName,getApplication]", [$config])->makePartial();
        $command->shouldReceive('setName');
        $command->shouldReceive('getApplication')->andReturn($application);

        $command->execute($input, $output);
    }
}
