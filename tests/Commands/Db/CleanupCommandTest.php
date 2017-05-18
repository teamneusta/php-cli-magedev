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

namespace TeamNeusta\Magedev\Test\Commands\Db;

use Mockery as m;
use TeamNeusta\Magedev\Commands\Db\CleanupCommand;

/**
 * Class: CleanupCommandTest.
 *
 * @see \PHPUnit_Framework_TestCase
 */
class CleanupCommandTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function setUp()
    {
    }

    public function testExecute()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('getMagentoVersion')->andReturn('2');

        $fileHelper = m::mock('\TeamNeusta\Magedev\Runtime\Helper\FileHelper');
        $fileHelper->shouldReceive('findPath')
            ->with('var/data/magento2/cleanup.sql')
            ->andReturn('somePath');
        $fileHelper->shouldReceive('deleteFile')->with('cleanup.sql');

        $shellService = m::mock('\TeamNeusta\Magedev\Services\ShellService');
        $shellService->shouldReceive('execute')
            ->with('cp somePath .');

        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $dockerService->shouldReceive('execute')->with('mysql -f < cleanup.sql');

        $command = new CleanupCommand(
            $config,
            $fileHelper,
            $shellService,
            $dockerService
        );
        $command->execute($input, $output);
    }
}
