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

use Mockery as m;
use TeamNeusta\Magedev\Docker\Container\Repository\Mailcatcher;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;

/**
 * Class: MailcatcherTest.
 *
 * @see \PHPUnit_Framework_TestCase
 */
class MailcatcherTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testGetImage()
    {
        $config = m::mock(Config::class);
        $imageFactory = m::mock(ImageFactory::class);
        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");
        $container = new Mailcatcher($config, $imageFactory, $nameBuilder);
        self::assertSame('schickling/mailcatcher', $container->getImage());
    }

    public function testGetName()
    {
        $config = m::mock(Config::class);
        $imageFactory = m::mock(ImageFactory::class);
        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");
        $container = new Mailcatcher($config, $imageFactory, $nameBuilder);
        self::assertSame('mailcatcher', $container->getName());
    }
}
