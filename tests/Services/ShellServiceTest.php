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

namespace TeamNeusta\Magedev\Test\Services;

use Mockery as m;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Services\ShellService;

/**
 * Class: ShellServiceTest.
 *
 * @see \PHPUnit_Framework_TestCase
 */
class ShellServiceTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testExecuteWithVerbosity()
    {
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput');
        $output->shouldReceive('getVerbosity')->andReturn(OutputInterface::VERBOSITY_VERBOSE);
        $output->shouldReceive('writeln')->with('execute: pwd')->times(1);
        $service = new ShellService($output);
        $service->execute('pwd');
    }

    public function testBashUsesWorkingDirectory()
    {
        $service = m::mock('\TeamNeusta\Magedev\Services\ShellService')->makePartial();
        $service->shouldReceive('execute')->with('cd Source && bin/magento');
        $service->wd('Source')->bash('bin/magento');
    }
}
