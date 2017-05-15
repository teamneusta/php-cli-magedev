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
use TeamNeusta\Magedev\Docker\Image\Repository\Varnish4;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;
use Docker\Context\ContextBuilder;

/**
 * Class: Varnish4Test
 *
 * @see \PHPUnit_Framework_TestCase
 */
class Varnish4Test extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testConfigure()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive("get")->with("env_vars")->andReturn([]);
        $config->shouldReceive("optionExists")->with("proxy")->andReturn(true);
        $config->shouldReceive("get")->with("proxy")->andReturn([
          "HTTP" => "someproxy.com:8872"
        ]);

        $imageFactory = m::mock(ImageFactory::class);
        $fileHelper = m::mock(FileHelper::class);
        $fileHelper->shouldReceive("read")->with("var/Docker/varnish/start.sh")->andReturn("start.sh content");

        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,add,run,from]");

        $contextBuilder->shouldReceive("from")->with("ubuntu:16.04");

        $contextBuilder->shouldReceive("run")
            ->with("usermod -u " . getmyuid() . " www-data");
        $contextBuilder->shouldReceive("run")
            ->with("echo \"Acquire::http::Proxy \\\"someproxy.com:8872;\\\" > /etc/apt/apt.conf\"");
        $contextBuilder->shouldReceive("run")
            ->with("pear config-set http_proxy  someproxy.com:8872");

        $contextBuilder->shouldReceive("run")
            ->with("pear config-set http_proxy  someproxy.com:8872");


        $contextBuilder->shouldReceive("run")->with("apt-get update");
        $contextBuilder->shouldReceive("run")->with("apt-get upgrade -yqq");

        $contextBuilder->shouldReceive("run")->with("apt-get install curl -y");
        $contextBuilder->shouldReceive("run")->with("curl -O https://repo.varnish-cache.org/source/varnish-4.1.4.tar.gz");


        $contextBuilder->shouldReceive("run")->with("apt-get install -y automake");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y   autotools-dev");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y   libedit-dev");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y   libjemalloc-dev");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y   libncurses-dev");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y   libpcre3-dev");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y   libtool");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y   pkg-config");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y   python-docutils");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y   python-sphinx");
        $contextBuilder->shouldReceive("run")->with("apt-get install -yqq curl");
        $contextBuilder->shouldReceive("run")->with("apt-get install -yqq supervisor");
        $contextBuilder->shouldReceive("run")->with("apt-get install -yqq apt-transport-https");
        $contextBuilder->shouldReceive("run")->with("apt-get install build-essential -yqq");
        $contextBuilder->shouldReceive("run")->with("apt-get install python-docutils -yqq");
        $contextBuilder->shouldReceive("run")->with("apt-get install libncurses-dev -yqq");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y pkg-config");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y libpcre++-dev");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y libedit-dev");
        $contextBuilder->shouldReceive("run")->with("tar -xvf varnish-4.1.4.tar.gz");
        $contextBuilder->shouldReceive("run")->with("cd varnish-4.1.4 && sh autogen.sh");
        $contextBuilder->shouldReceive("run")->with("cd varnish-4.1.4 && ./configure && make && make install");


        $contextBuilder->shouldReceive("expose")->with("80");
        $contextBuilder->shouldReceive("add")->with("/start.sh", "start.sh content");
        $contextBuilder->shouldReceive("cmd")->with("supervisord");

        $contextBuilder->shouldReceive("__destruct");

        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");

        $image = new Varnish4(
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
