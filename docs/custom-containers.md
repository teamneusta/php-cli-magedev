# Custom containers

Lets see how you can change existing preconfigured docker containers with custom ones, e.g. to change version of specific services or add other custom containers. Magedev does not use an orchestrator like `docker-composer` or `Dockerfile`s directly. Instead all configuration is written in PHP to avoid dependency to `docker-composer`. This configuration is done in `src/Docker/Container` and `src/Docker/Image` respectively. You can change these classes and replace them with custom ones.

## Example: changing Version of mysql

Create a file `.magedev/Docker/Image/Repository/Mysql.php` in your project root with following content. The original file path is `src/Docker/Image/Repository/Mysql.php` and you may copy the content from there as well.

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
     * Class Mysql.
     */
    class Mysql extends AbstractImage
    {
        /**
         * getBuildName.
         *
         * @return string
         */
        public function getBuildName()
        {
            return $this->nameBuilder->buildName(
                 $this->getName()
            );
        }

        /**
         * configure.
         */
        public function configure()
        {
            $this->name('mysql');
            $this->from('mysql:5.6');

            $uid = getmyuid();
            $this->run('usermod -u '.$uid.' mysql');

            // addresses permission error, cannot bind socket
            $this->run("chmod -R 777 /var/run/mysqld/");

            $this->addFile("var/Docker/mysql/mysql.cnf", "/etc/mysql/conf.d/z99-docker.cnf");
            $this->run("chmod 644 /etc/mysql/conf.d/z99-docker.cnf");

            $this->addFile("var/Docker/mysql/my.cnf","/root/.my.cnf");
            $this->addFile("var/Docker/mysql/my.cnf","/var/www/.my.cnf");
        }
    }

Change this file e.g. to use mysql:5.5 by changing this line:

    $this->from('mysql:5.5');

## Example using custom MongoDB container

.magedev/Docker/Container/Repository/MongoDB.php


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

    namespace TeamNeusta\Magedev\Docker\Container\Repository;

    use TeamNeusta\Magedev\Docker\Container\AbstractContainer;

    /**
     * Class: MongoDB.
     *
     * @see AbstractContainer
     */
    class MongoDB extends AbstractContainer
    {
        /**
         * getName.
         */
        public function getName()
        {
            return 'mongodb';
        }

        /**
         * getImage.
         */
        public function getImage()
        {
            return 'mongo:3.0';
        }
    }

Add this to your `magedev.json` to include the custom container and link it:

    "docker": {
      "containers": [
        "ElasticSearch",
        "Mailcatcher",
        "Main",
        "Mysql",
        "Redis",
        "Varnish",
        "MongoDB"
      ],
      "links": {
        "main": ["mysql", "redis", "elasticsearch", "mongodb"]
      }
    ]

## Apply changes

To apply changes made to docker configuration use the reinit command:

    magedev docker:reinit
