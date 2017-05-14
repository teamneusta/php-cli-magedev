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
use TeamNeusta\Magedev\Docker\Image\Repository\Main;
/* use TeamNeusta\Magedev\Docker\Context; */
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;
use Docker\Context\ContextBuilder;

/**
 * Class: MainTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class MainTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testConfigure()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive("getMagentoVersion")->andReturn(2);
        $config->shouldReceive("get")->with("document_root")->andReturn("/var/www/html/Source");
        $config->shouldReceive("get")->with("gateway")->andReturn("172.20.0.1");

        $imageFactory = m::mock(ImageFactory::class);
        $imageFactory->shouldReceive("create")->with("Php7")->times(1);

        $fileHelper = m::mock(FileHelper::class);
        $fileHelper->shouldReceive('read')
            ->with("var/Docker/main/000-default.conf")
            ->andReturn("someContent with vars: \$DOCUMENT_ROOT");

        $fileHelper->shouldReceive('read')
            ->with("var/Docker/main/php.ini")
            ->andReturn("someContent with \$GATEWAY");

        $fileHelper->shouldReceive('read')
            ->with("var/Docker/mysql/my.cnf")
            ->andReturn("mysql config");
        $fileHelper->shouldReceive('read')
            ->with("var/Docker/main/loadssh.sh")
            ->andReturn("loadssh file content");
        $fileHelper->shouldReceive('read')
            ->with("var/Docker/vendor/mini_sendmail-1.3.9/mini_sendmail")
            ->andReturn("sendmail bin");

        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,add]");
        $contextBuilder->shouldReceive("from");
        $contextBuilder->shouldReceive("__destruct")->andReturn(null);
        $contextBuilder->shouldReceive("add")
            ->with(
                "/etc/apache2/sites-available/000-default.conf",
                "someContent with vars: /var/www/html/Source"
            );
        $contextBuilder->shouldReceive("add")
            ->with(
                "/etc/apache2/sites-enabled/000-default.conf",
                "someContent with vars: /var/www/html/Source"
            );
        $contextBuilder->shouldReceive("add")
            ->with(
                "/usr/local/etc/php/php.ini",
                "someContent with 172.20.0.1"
            );
        $contextBuilder->shouldReceive("add")
            ->with(
                "/root/.my.cnf",
                "mysql config"
            );
        $contextBuilder->shouldReceive("add")
            ->with(
                "/var/www/.my.cnf",
                "mysql config"
            );
        $contextBuilder->shouldReceive("add")
            ->with(
                "/usr/bin/loadssh.sh",
                "loadssh file content"
            );
        $contextBuilder->shouldReceive("add")
            ->with(
                "/usr/bin/mini_sendmail",
                "sendmail bin"
            );

        $image = new Main(
            $config,
            $imageFactory,
            $fileHelper,
            $contextBuilder
        );
        $image->configure();
    }

    public function testPhp5ImageForMagento1()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive("getMagentoVersion")->andReturn(1);
        $config->shouldReceive("get")->with("document_root");
        $config->shouldReceive("get")->with("gateway")->andReturn("172.20.0.1");

        $imageFactory = m::mock(ImageFactory::class);
        $imageFactory->shouldReceive("create")->with("Php5")->times(1);

        $fileHelper = m::mock(FileHelper::class);
        $fileHelper->shouldReceive('read');

        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,add]");
        $contextBuilder->shouldReceive("from");
        $contextBuilder->shouldReceive("__destruct")->andReturn(null);
        $contextBuilder->shouldReceive("add");

        $image = new Main(
            $config,
            $imageFactory,
            $fileHelper,
            $contextBuilder
        );
        $image->configure();
    }
}
