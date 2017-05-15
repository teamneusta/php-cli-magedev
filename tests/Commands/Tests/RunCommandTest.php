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

namespace TeamNeusta\Magedev\Test\Commands\Tests;

use \Mockery as m;
use TeamNeusta\Magedev\Commands\Tests\RunCommand;

/**
 * Class: RunCommandTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class RunCommandTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testExecute()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('optionExists')->with("phpunitxml_path")->andReturn(false);

        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $dockerService->shouldReceive("execute")
            ->with(
                "vendor/bin/phpunit -c /var/www/html/phpunit.xml"
              );
        $command = new RunCommand(
            $config,
            $dockerService
        );
        $command->execute($input, $output);
    }

    public function testExecuteWithCustomXmlPath()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('optionExists')->with("phpunitxml_path")->andReturn(true);
        $config->shouldReceive('get')->with("phpunitxml_path")->andReturn("Source/phpunit.xml");


        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $dockerService->shouldReceive("execute")
            ->with(
                "vendor/bin/phpunit -c Source/phpunit.xml"
              );
        $command = new RunCommand(
            $config,
            $dockerService
        );
        $command->execute($input, $output);

    }
}
