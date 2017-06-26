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

namespace TeamNeusta\Magedev\Test\Commands\Grunt;

use Mockery as m;
use TeamNeusta\Magedev\Commands\Grunt\RefreshCommand;

/**
 * Class: RefreshCommandTest.
 *
 * @see \PHPUnit_Framework_TestCase
 */
class RefreshCommandTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testExecute()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('getMagentoVersion')->andReturn('2');

        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $dockerService->shouldReceive('execute')->with('/usr/local/bin/grunt refresh');
        $command = new RefreshCommand(
            $config,
            $dockerService
        );
        $command->execute($input, $output);
    }
}
