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
 * Class Main
 */
class Main extends AbstractImage
{
    /**
     * configure
     */
    public function configure()
    {
        $this->name("main");

        // PHP Image is selected based on magento version
        $magentoVersion = $this->context->getConfig()->getMagentoVersion();

        if ($this->context->getConfig()->getMagentoVersion() == "2") {
            $this->from(new \TeamNeusta\Magedev\Docker\Image\Repository\Php7($this->context));
        }

        if ($this->context->getConfig()->getMagentoVersion() == "1") {
            $this->from(new \TeamNeusta\Magedev\Docker\Image\Repository\Php5($this->context));
        }

        // TODO: have something like a simple template engine to replace vars
        // like DOCUMENT_ROOT AND $GATEWAY ?

        $documentRoot = $this->context->getConfig()->getDocumentRootPath();
        $vhostConfig = $this->context->getFileHelper()->read("var/Docker/main/000-default.conf");
        $vhostConfig = str_replace("\$DOCUMENT_ROOT", $documentRoot, $vhostConfig);

        $this->add("/etc/apache2/sites-available/000-default.conf", $vhostConfig);
        $this->add("/etc/apache2/sites-enabled/000-default.conf", $vhostConfig);

        // $GATEWAY
        $gatewayIp = $this->context->getConfig()->getGateway();
        if (empty($gatewayIp)) {
            throw new \Exception("no gateway ip found");
        }
        $phpIni = $this->context->getFileHelper()->read("var/Docker/main/php.ini");
        $phpIni = str_replace("\$GATEWAY", $gatewayIp, $phpIni);
        $this->add("/usr/local/etc/php/php.ini", $phpIni);
        $this->run("chmod 775 /usr/local/etc/php/php.ini"); // for www-data to read it

        $this->addFile("var/Docker/mysql/my.cnf","/root/.my.cnf");
        $this->addFile("var/Docker/mysql/my.cnf","/var/www/.my.cnf");
        $this->run("chown www-data:www-data /var/www/.my.cnf");

        $this->run("curl -O https://getcomposer.org/composer.phar");
        $this->run("mv composer.phar /usr/bin/composer");
        $this->run("chmod 777 /usr/bin/composer");
        $this->run("chmod +x /usr/bin/composer");

        $this->addFile("var/Docker/main/loadssh.sh", "/usr/bin/loadssh.sh");
        $this->run("chmod 777 /usr/bin/loadssh.sh");
        $this->run("chmod +x /usr/bin/loadssh.sh");

        $this->addFile("var/Docker/vendor/mini_sendmail-1.3.9/mini_sendmail", "/usr/bin/mini_sendmail");
        $this->run("chmod +x /usr/bin/mini_sendmail");
    }
}
