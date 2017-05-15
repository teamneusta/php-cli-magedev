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
use TeamNeusta\Magedev\Docker\Container\Repository\Main;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;

/**
 * Class: MainTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class MainTest extends \TeamNeusta\Magedev\Test\TestCase
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

        /* $config = m::mock("TeamNeusta\Magedev\Runtime\Config", [$input, $fileHelper])->makePartial(); */
        $imageFactory = m::mock(ImageFactory::class);
        $config = new Config($input, $fileHelper);
        $config->load();
        $config->set("project_path", "/some/path/to/project");
        $config->set("home_path", "/home/someuser");
        $config->set("network_id", "582f685244a4");
        $config->set("env_vars", ["MYSQL_USER" => "root", "USERID" => 1000]);

        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");

        $main = new Main($config, $imageFactory, $nameBuilder);
        $containerConfig = $main->getConfig();
        self::assertSame(
            "582f685244a4",
            $containerConfig->getNetworkingConfig()->getEndpointsConfig()["magedev_default"]->getNetworkID()
        );
        self::assertSame(["MYSQL_USER=root", "USERID=1000"], $containerConfig->getEnv());

        self::assertSame(
            [
                "/some/path/to/project:/var/www/html:rw",
                "/home/someuser/.composer:/var/www/.composer:rw",
                "/home/someuser/.ssh:/var/www/.ssh:rw"
            ],
            $containerConfig->getHostConfig()->getBinds()
        );
    }

    public function testGetImage()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive("get")->with("env_vars")->andReturn([]);
        $fileHelper = m::mock(FileHelper::class);
        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,add,run,from]");
        $contextBuilder->shouldReceive("__destruct");
        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");
        $imageFactory = new ImageFactory(
            $config,
            $fileHelper,
            $imageApiFactory,
            $nameBuilder
        );

        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");

        $main = new Main($config, $imageFactory, $nameBuilder);
        self::assertSame(\TeamNeusta\Magedev\Docker\Image\Repository\Main::class, get_class($main->getImage()));
    }

    public function testGetName()
    {
        $config = m::mock(Config::class);
        $imageFactory = m::mock(ImageFactory::class);
        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");
        $main = new Main($config, $imageFactory, $nameBuilder);
        self::assertSame("main", $main->getName());
    }
}
