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

use Mockery as m;
use TeamNeusta\Magedev\Commands\Magento\RefreshCommand;

/**
 * Class: RefreshCommandTest.
 *
 * @see \PHPUnit_Framework_TestCase
 */
class RefreshCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config', [
            'getMagentoVersion' => 2,
        ]);

        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $dockerService->shouldReceive('execute')->with('rm -rf var/generation/*');
        $dockerService->shouldReceive('execute')->with('rm -rf var/di/*');
        $dockerService->shouldReceive('execute')->with('rm -rf var/cache/*');
        $dockerService->shouldReceive('execute')->with('rm -rf var/view_preprocessed/*');
        $dockerService->shouldReceive('execute')->with('rm -rf pub/static/_requirejs');
        $dockerService->shouldReceive('execute')->with('rm -rf pub/static/adminhtml');
        $dockerService->shouldReceive('execute')->with('rm -rf pub/static/frontend');

        $command = new RefreshCommand($config, $dockerService);
        $command->execute($input, $output);
    }
}
