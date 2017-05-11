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
use TeamNeusta\Magedev\Commands\Init\PermissionsCommand;

/**
 * Class: PermissionsCommandTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class PermissionsCommandTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testExecute()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config');
        $config->shouldReceive('getMagentoVersion')->andReturn('2');
        $config->shouldReceive('get')->with('source_folder')->andReturn('Source');

        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');
        $dockerService->shouldReceive("execute")->with("chown -R www-data:users /var/www/html", ['user' => 'root']);
        $dockerService->shouldReceive("execute")->with("chown -R www-data:users /var/www/.composer", ['user' => 'root']);
        $dockerService->shouldReceive("execute")->with("chown -R www-data:users /var/www/.ssh", ['user' => 'root']);
        $dockerService->shouldReceive("execute")->with("chown -R www-data:users /var/www/modules", ['user' => 'root']);
        $dockerService->shouldReceive("execute")->with("chown -R www-data:users /var/www/composer-cache", ['user' => 'root']);
        $dockerService->shouldReceive("execute")->with("cd /var/www/html && chmod -R 775 Source", ['user' => 'root']);
        $dockerService->shouldReceive("execute")->with("usermod -u ".getmyuid()." mysql",
        ['user' => 'root', 'container' => 'mysql']);
        $command = new PermissionsCommand(
            $config,
            $dockerService
        );
        $command->execute($input, $output);
    }
}
