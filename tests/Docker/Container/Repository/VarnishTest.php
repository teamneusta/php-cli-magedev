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
use TeamNeusta\Magedev\Docker\Container\Repository\Varnish;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;

/**
 * Class: VarnishTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class VarnishTest extends \TeamNeusta\Magedev\Test\TestCase
{
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
        $container = new Varnish($config, $imageFactory);
        self::assertSame(\TeamNeusta\Magedev\Docker\Image\Repository\Varnish::class, get_class($container->getImage()));
    }

    public function testGetName()
    {
        $config = m::mock(Config::class);
        $imageFactory = m::mock(ImageFactory::class);
        $container = new Varnish($config, $imageFactory);
        self::assertSame("varnish", $container->getName());
    }
}
