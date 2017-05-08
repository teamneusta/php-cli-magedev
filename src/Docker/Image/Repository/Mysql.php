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
 * Class Mysql
 */
class Mysql extends AbstractImage
{
    /**
     * configure
     */
    public function configure()
    {
        $this->name('mysql');
        $this->from('mysql:5.6');

        $uid = getmyuid();
        $this->run("usermod -u " . $uid . " mysql");

        $this->addFile("var/Docker/conf/mysql.cnf", "/etc/mysql/conf.d/z99-docker.cnf");
        $this->addFile("var/Docker/conf/my.cnf","/root/.my.cnf");
        $this->addFile("var/Docker/conf/my.cnf","/var/www/.my.cnf");
    }
}
