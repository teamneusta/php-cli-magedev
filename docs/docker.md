# Docker

This will build and bring up all required docker containers.

    magedev docker:start

You may inspect this using `docker ps`:

    $ docker ps
    CONTAINER ID        IMAGE                    COMMAND                  CREATED             STATUS              PORTS                                            NAMES
    aebd9e389022        docker_php-5.6           "apache2-foreground"     3 minutes ago       Up 3 minutes        0.0.0.0:80->80/tcp, 0.0.0.0:443->443/tcp         magento1-test
    580b3d71154b        docker_mysql             "docker-entrypoint.sh"   3 minutes ago       Up 3 minutes        0.0.0.0:3306->3306/tcp                           docker_mysql_1
    eb1db447e388        docker_elasticsearch     "/docker-entrypoint.s"   3 minutes ago       Up 3 minutes        9200/tcp, 9300/tcp                               docker_elasticsearch_1
    9e588879ecd7        schickling/mailcatcher   "mailcatcher -f --ip="   2 hours ago         Up 3 minutes        0.0.0.0:1025->1025/tcp, 0.0.0.0:1080->1080/tcp   docker_mailcatcher_1
