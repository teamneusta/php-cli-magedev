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

namespace TeamNeusta\Magedev\Test\Docker\Image;

use \Mockery as m;
use TeamNeusta\Magedev\Docker\Container\Repository\Mysql;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;

/**
 * Class: MysqlTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class MysqlTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testGetConfig()
    {
        $input = m::mock('Symfony\Component\Console\Input\InputInterface');
        $input->shouldReceive("getArgument")->andReturn(null);
        $fileHelper = m::mock('\TeamNeusta\Magedev\Runtime\Helper\FileHelper');
        $fileHelper->shouldReceive('findPath');
        $fileHelper->shouldReceive('expandPath');
        $fileHelper->shouldReceive('fileExists')->andReturn(true);
        $fileHelper->shouldReceive('read')->andReturn("[]");

        $imageFactory = m::mock(ImageFactory::class);
        $config = new Config($input, $fileHelper);
        $config->load();
        $config->set("project_path", "/some/path/to/project");
        $config->set("home_path", "/home/someuser");
        $config->set("network_id", "582f685244a4");
        $config->set("env_vars", ["MYSQL_USER" => "root", "USERID" => 1000]);

        $main = new Mysql($config, $imageFactory);
        $containerConfig = $main->getConfig();
        self::assertSame(
            [
                "/some/path/to/project/mysql:/var/lib/mysql:rw"
            ],
            $containerConfig->getHostConfig()->getBinds()
        );
    }

    public function testGetImage()
    {
        $config = m::mock(Config::class);
        $fileHelper = m::mock(FileHelper::class);
        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,add,run,from]");
        $contextBuilder->shouldReceive("__destruct");
        $imageFactory = new ImageFactory(
            $config,
            $fileHelper,
            $contextBuilder
        );

        $mysql = new Mysql($config, $imageFactory);
        self::assertSame(\TeamNeusta\Magedev\Docker\Image\Repository\Mysql::class, get_class($mysql->getImage()));
    }

    public function testGetName()
    {
        $config = m::mock(Config::class);
        $imageFactory = m::mock(ImageFactory::class);
        $mysql = new Mysql($config, $imageFactory);
        self::assertSame("mysql", $mysql->getName());
    }
}
