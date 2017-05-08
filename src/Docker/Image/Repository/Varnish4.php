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

namespace TeamNeusta\Magedev\Docker\Image\Repository;

use TeamNeusta\Magedev\Docker\Image\AbstractImage;

/**
 * Class Varnish4
 */
class Varnish4 extends AbstractImage
{
    /**
     * configure
     */
    public function configure()
    {
        $this->name('varnish4');
        $this->from('ubuntu:16.04');

        if (array_key_exists("HTTP_PROXY", $this->context->getEnvVars())) {
            $httpProxy = $this->context->getEnvVars()["HTTP_PROXY"];
            $this->run("echo \"Acquire::http::Proxy \\\"".$httpProxy.";\\\" > /etc/apt/apt.conf\"");
        }
        if (array_key_exists("HTTPS_PROXY", $this->context->getEnvVars())) {
            $httpsProxy = $this->context->getEnvVars()["HTTPS_PROXY"];
            $this->run("echo \"Acquire::https::Proxy \\\"".$httpsProxy.";\\\" > /etc/apt/apt.conf\"");
        }
        // Update the package repository and install applications
        $this->run("apt-get update");
        $this->run("apt-get upgrade -yqq");

        $this->run("apt-get install curl -y");
        $this->run("curl -O https://repo.varnish-cache.org/source/varnish-4.1.4.tar.gz");


        $this->run("apt-get install -y automake");
        $this->run("apt-get install -y   autotools-dev");
        $this->run("apt-get install -y   libedit-dev");
        $this->run("apt-get install -y   libjemalloc-dev");
        $this->run("apt-get install -y   libncurses-dev");
        $this->run("apt-get install -y   libpcre3-dev");
        $this->run("apt-get install -y   libtool");
        $this->run("apt-get install -y   pkg-config");
        $this->run("apt-get install -y   python-docutils");
        $this->run("apt-get install -y   python-sphinx");
        $this->run("apt-get install -yqq curl");
        $this->run("apt-get install -yqq supervisor");
        $this->run("apt-get install -yqq apt-transport-https");

        $this->run("apt-get install build-essential -yqq");
        $this->run("apt-get install python-docutils -yqq");
        $this->run("apt-get install libncurses-dev -yqq");
        $this->run("apt-get install -y pkg-config");
        $this->run("apt-get install -y libpcre++-dev");
        $this->run("apt-get install -y libedit-dev");


        $this->run("tar -xvf varnish-4.1.4.tar.gz");
        $this->run("cd varnish-4.1.4 && sh autogen.sh");
        $this->run("cd varnish-4.1.4 && ./configure && make && make install");


        $this->expose("80");
        $this->addFile("var/Docker/conf/varnish/start.sh", "/start.sh");
        $this->cmd("supervisord");
    }
}
