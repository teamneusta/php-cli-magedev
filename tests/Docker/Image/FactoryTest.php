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
use TeamNeusta\Magedev\Docker\Image\Factory;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;
use Docker\Context\ContextBuilder;

/**
 * Class: FactoryTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class FactoryTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testCreate()
    {
        $config = m::mock(Config::class);
        $fileHelper = m::mock(FileHelper::class);
        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,add,run,from]");
        $contextBuilder->shouldReceive("__destruct");

        $factory = new Factory(
            $config,
            $fileHelper,
            $contextBuilder
        );

        self::assertSame(get_class($factory->create("Main")), \TeamNeusta\Magedev\Docker\Image\Repository\Main::class);
        self::assertSame(get_class($factory->create("Mysql")), \TeamNeusta\Magedev\Docker\Image\Repository\Mysql::class);
        self::assertSame(get_class($factory->create("Php5")), \TeamNeusta\Magedev\Docker\Image\Repository\Php5::class);
        self::assertSame(get_class($factory->create("Php7")), \TeamNeusta\Magedev\Docker\Image\Repository\Php7::class);
        self::assertSame(get_class($factory->create("Varnish")), \TeamNeusta\Magedev\Docker\Image\Repository\Varnish::class);
        self::assertSame(get_class($factory->create("Varnish4")), \TeamNeusta\Magedev\Docker\Image\Repository\Varnish4::class);
    }
}
