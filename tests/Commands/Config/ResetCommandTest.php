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

use \Mockery as m;
use TeamNeusta\Magedev\Commands\Config\ResetCommand;

/**
 * Class: ResetCommandTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class ResetCommandTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function setUp()
    {

    }

    public function testImportNonExistingValue()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('getMagentoVersion')->andReturn("2");

        $fileHelper = m::mock('\TeamNeusta\Magedev\Runtime\Helper\FileHelper');
        $fileHelper->shouldReceive("findPath")
            ->with("var/data/magento2/config.yml")
            ->andReturn("/home/user/magedev/var/data/magento2/config.yml");
        $fileHelper->shouldReceive("fileExists")
            ->with("/home/user/magedev/var/data/magento2/config.yml")
            ->andReturn(true);
        $fileHelper->shouldReceive("read")
            ->with("/home/user/magedev/var/data/magento2/config.yml")
            ->andReturn("design/head/default_title: \"Installed by Magedev\"");

        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $dockerService->shouldReceive("execute")
            ->with(
                "mysql --execute \"select * from core_config_data where path = 'design/head/default_title';\"",
                ['interactive' => false]
              );
        $dockerService->shouldReceive("execute")
            ->with(
                "mysql --execute \"INSERT core_config_data (scope, scope_id, path, value) VALUES ('default', 0, 'design/head/default_title', Installed by Magedev);\"",
                ['interactive' => false]
              );
        $command = new ResetCommand(
            $config,
            $fileHelper,
            $dockerService
        );
        $command->execute($input, $output);
    }

    public function testImportUpdatesValue()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('getMagentoVersion')->andReturn("2");

        $fileHelper = m::mock('\TeamNeusta\Magedev\Runtime\Helper\FileHelper');
        $fileHelper->shouldReceive("findPath")
            ->with("var/data/magento2/config.yml")
            ->andReturn("/home/user/magedev/var/data/magento2/config.yml");
        $fileHelper->shouldReceive("fileExists")
            ->with("/home/user/magedev/var/data/magento2/config.yml")
            ->andReturn(true);
        $fileHelper->shouldReceive("read")
            ->with("/home/user/magedev/var/data/magento2/config.yml")
            ->andReturn("design/head/default_title: \"Installed by Magedev\"");

        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $dockerService->shouldReceive("execute")
            ->with(
                "mysql --execute \"select * from core_config_data where path = 'design/head/default_title';\"",
                ['interactive' => false]
            )
            ->andReturn("someValueForExistence");
        $dockerService->shouldReceive("execute")
            ->with(
                "mysql --execute \"UPDATE core_config_data SET value='Installed by Magedev' WHERE path='design/head/default_title'\"",
                ['interactive' => false]
              );
        $command = new ResetCommand(
            $config,
            $fileHelper,
            $dockerService
        );
        $command->execute($input, $output);
    }

    public function testLoadOtherConfigFileForMagento1()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);
        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('getMagentoVersion')->andReturn("1");
        $fileHelper = m::mock('\TeamNeusta\Magedev\Runtime\Helper\FileHelper');
        $fileHelper->shouldReceive('findPath')->with("var/data/magento1/config.yml");
        $fileHelper->shouldReceive('fileExists');
        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $command = new ResetCommand(
            $config,
            $fileHelper,
            $dockerService
        );
        $command->execute($input, $output);
    }
}
