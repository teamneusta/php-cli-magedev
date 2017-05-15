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
use TeamNeusta\Magedev\Docker\Image\Repository\Php7;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;
use Docker\Context\ContextBuilder;

/**
 * Class: Php7Test
 *
 * @see \PHPUnit_Framework_TestCase
 */
class Php7Test extends \TeamNeusta\Magedev\Test\TestCase
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

        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,add,run]");
        $contextBuilder->shouldReceive("run")
            ->with("usermod -u " . getmyuid() . " www-data");
        $contextBuilder->shouldReceive("run")
            ->with("echo \"Acquire::http::Proxy \\\"someproxy.com:8872;\\\" > /etc/apt/apt.conf\"");
        $contextBuilder->shouldReceive("run")
            ->with("pear config-set http_proxy  someproxy.com:8872");

        $contextBuilder->shouldReceive("run")
            ->with("pear config-set http_proxy  someproxy.com:8872");
        $contextBuilder->shouldReceive("run")->with("apt-get update");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y libxslt1.1 libxslt1-dev");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y zlib1g-dev");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng12-dev");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y libicu-dev");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y mysql-client");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y npm");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y git");
        $contextBuilder->shouldReceive("run")->with("apt-get install -y libmagickwand-dev --no-install-recommends");
        $contextBuilder->shouldReceive("run")->with("docker-php-ext-install -j$(nproc) xsl");
        $contextBuilder->shouldReceive("run")->with("docker-php-ext-install -j$(nproc) zip");
        $contextBuilder->shouldReceive("run")->with("docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/");
        $contextBuilder->shouldReceive("run")->with("docker-php-ext-install -j$(nproc) gd");
        $contextBuilder->shouldReceive("run")->with("docker-php-ext-install -j$(nproc) intl");
        $contextBuilder->shouldReceive("run")->with("docker-php-ext-install -j$(nproc) mcrypt");
        $contextBuilder->shouldReceive("run")->with("docker-php-ext-install -j$(nproc) pdo_mysql");
        $contextBuilder->shouldReceive("run")->with("docker-php-ext-install -j$(nproc) soap");
        $contextBuilder->shouldReceive("run")->with("docker-php-ext-install -j$(nproc) bcmath");
        $contextBuilder->shouldReceive("run")->with("pear upgrade");
        $contextBuilder->shouldReceive("run")->with("pecl upgrade");
        $contextBuilder->shouldReceive("run")->with("pecl install xdebug");
        $contextBuilder->shouldReceive("run")->with("yes '' | pecl install imagick");
        $contextBuilder->shouldReceive("run")->with("a2enmod rewrite");
        $contextBuilder->shouldReceive("run")->with("mkdir /var/www/.composer");
        $contextBuilder->shouldReceive("run")->with("mkdir /var/www/.ssh");
        $contextBuilder->shouldReceive("run")->with("mkdir /var/www/modules");
        $contextBuilder->shouldReceive("run")->with("mkdir /var/www/composer-cache");
        $contextBuilder->shouldReceive("run")->with("chown www-data:www-data /var/www/html");
        $contextBuilder->shouldReceive("run")->with("chown www-data:www-data /var/www/.composer");
        $contextBuilder->shouldReceive("run")->with("chown www-data:www-data /var/www/.ssh");
        $contextBuilder->shouldReceive("run")->with("chown www-data:www-data /var/www/modules");
        $contextBuilder->shouldReceive("__destruct");

        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");

        $image = new Php7(
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
