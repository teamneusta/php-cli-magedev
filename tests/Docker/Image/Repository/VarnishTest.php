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
use TeamNeusta\Magedev\Docker\Image\Repository\Varnish;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;
use Docker\Context\ContextBuilder;

/**
 * Class: VarnishTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class VarnishTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testConfigure()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive("get")->with("env_vars")->andReturn([]);
        $config->shouldReceive("optionExists")->with("proxy")->andReturn(true);
        $config->shouldReceive("get")->with("proxy")->andReturn([
          "HTTP" => "someproxy.com:8872"
        ]);

        $image = m::mock(\TeamNeusta\Magedev\Docker\Image\Repository\Varnish4::class);
        $image->shouldReceive("getBuildName")->andReturn("magedev-project-varnish4");

        $imageFactory = m::mock(ImageFactory::class);
        $imageFactory->shouldReceive("create")->andReturn($image);
        $fileHelper = m::mock(FileHelper::class);

        $fileHelper->shouldReceive("read")->with("var/Docker/varnish/conf/supervisord.conf")->andReturn("supervisord.conf content");
        $fileHelper->shouldReceive("read")->with("var/Docker/varnish/etc/default/varnish")->andReturn("varnish content");
        $fileHelper->shouldReceive("read")->with("var/Docker/varnish/etc/varnish/default.vcl")->andReturn("default.vcl content");

        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,add,run,from]");

        $contextBuilder->shouldReceive("from")->with("magedev-project-varnish4")->times(1);

        $contextBuilder->shouldReceive("add")->with("/etc/supervisor/conf.d/supervisord.conf", "supervisord.conf content");
        $contextBuilder->shouldReceive("add")->with("/etc/default/varnish", "varnish content");
        $contextBuilder->shouldReceive("add")->with("/etc/varnish/default.vcl", "default.vcl content");


        $contextBuilder->shouldReceive("__destruct");

        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $imageApiFactory->shouldReceive("create")->with($image)->andReturnSelf();
        $imageApiFactory->shouldReceive("build")->times(1);

        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");

        $image = new Varnish(
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
