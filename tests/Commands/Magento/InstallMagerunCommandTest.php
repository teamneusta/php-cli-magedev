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
use TeamNeusta\Magedev\Commands\Magento\InstallMagerunCommand;

/**
 * Class: InstallMagerunCommandTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class InstallMagerunCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config', [
            'getMagentoVersion' => 2
        ]);
        $config->shouldReceive('get')->with('source_folder')->andReturn("Source");

        $shellService = m::mock('\TeamNeusta\Magedev\Services\ShellService');
        $shellService->shouldReceive("wd")->with("Source")->andReturnSelf();
        $shellService->shouldReceive("bash")->with("curl -O https://files.magerun.net/n98-magerun2.phar");
        $shellService->shouldReceive("bash")->with("mv n98-magerun2.phar bin/magerun");
        $shellService->shouldReceive("bash")->with("chmod +x bin/magerun");

        $command = new InstallMagerunCommand($config, $shellService);
        $command->execute($input, $output);
    }

    public function testMagento1ShouldReceiveOtherMagerunVersion()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config', [
            'getMagentoVersion' => 1
        ]);
        $config->shouldReceive('get')->with('source_folder')->andReturn("Source");

        $shellService = m::mock('\TeamNeusta\Magedev\Services\ShellService');
        $shellService->shouldReceive("wd")->with("Source")->andReturnSelf();
        $shellService->shouldReceive("bash")->with("curl -O https://files.magerun.net/n98-magerun.phar");
        $shellService->shouldReceive("bash")->with("mv n98-magerun.phar shell/magerun");
        $shellService->shouldReceive("bash")->with("chmod +x shell/magerun");

        $command = new InstallMagerunCommand($config, $shellService);
        $command->execute($input, $output);
    }

}
