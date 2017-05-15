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

namespace TeamNeusta\Magedev\Test\Docker\Image\Repository;

use \Mockery as m;
use TeamNeusta\Magedev\Docker\Image\Repository\Mysql;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;
use Docker\Context\ContextBuilder;

/**
 * Class: MysqlTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class MysqlTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testConfigure()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive("get")->with("env_vars")->andReturn([]);
        $imageFactory = m::mock(ImageFactory::class);
        $fileHelper = m::mock(FileHelper::class);
        $fileHelper->shouldReceive("read")->with("var/Docker/mysql/mysql.cnf")->andReturn("mysql.cnf content");
        $fileHelper->shouldReceive("read")->with("var/Docker/mysql/my.cnf")->andReturn("my.cnf content");

        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,run,add,from]")->makePartial();
        $contextBuilder->shouldReceive("from")
            ->with("mysql:5.6")->times(1);
        $contextBuilder->shouldReceive("run")
            ->with("usermod -u " . getmyuid() . " mysql")->times(1);

        $contextBuilder->shouldReceive("add")
            ->with("/etc/mysql/conf.d/z99-docker.cnf", "mysql.cnf content")->times(1);
        $contextBuilder->shouldReceive("add")
            ->with("/root/.my.cnf", "my.cnf content")->times(1);
        $contextBuilder->shouldReceive("add")
            ->with("/var/www/.my.cnf", "my.cnf content")->times(1);

        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");

        $image = new Mysql(
            $config,
            $imageFactory,
            $fileHelper,
            $contextBuilder,
            $imageApiFactory,
            $nameBuilder
        );
        $image->configure();
    }
}
