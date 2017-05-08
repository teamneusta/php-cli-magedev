# Switching projects

To start a different project, make sure your current project is stopped. Otherwise, your portmapping may fail, see [port mapping](port-mapping.md) for more information.

    magedev docker:stop

Change to the directory of your other project and just start it with:

    magedev docker:start

# Database

The database service is running inside a container, but it uses a volume and stores all your data inside a folder `mysql` in your project root directory. Because magedev configures a port forwarding for 3306, you may access the database on your local host with:

    mysql -u magento -pmagento -h 0.0.0.0 magento

Or use magedev shortcut:

    magedev docker:cli:mysql

This will drop a mysql shell inside the docker container.

In case you are stug and you want to start fresh, stop your containers, remove `mysql` folder and rebuild your containers. A fresh mysql folder will be recreated on startup.

    magedev docker:stop
    rm -rf mysql/
    magedev docker:start

# Mailcatcher

Mailcatcher is preconfigured and can be accessed in your browser with:

    http://localhost:1080

# Tasks inside your container

You may need to run some custom commands in the container. You can access the container with:

    magedev docker:cli:ssh

For some everyday tasks, `magedev` offers shurtcuts like:

    magento:cache:clean      cleans magento cache
    magento:refresh          deletes generated files var/generation, var/di ... only Magento2
    magento:reindex          executes bin/magento indexer:reindex inside container
    magento:upgrade          executes bin/magento setup:upgrade inside container

The cool thing using these tasks is, that they will work, regardless of the used Magento version. The work on Magento1 as well as Magento2. All handling of differences is done for you.

# Working with grunt

On Magento2 you have grunt tasks available. These are:

    grunt:refresh            runs refresh inside container
    grunt:watch              runs watch inside container


# Running tests

Magedev provides tasks to run and debug phpunit tests. It assumes to find a `phpunit.xml` in your project root directory.

    tests:run                runs tests
    tests:debug              debug tests

If you have another location for your `phpunit.xml` file, specify it in your `magedev.json` like this:

    "phpunitxml_path": "/var/www/html/Source/Magento/bin/phpunit.xml",

The cool thing is, that it becomes very easy to use xdebug for your unit tests. Place some breakpoints, hit the button `Start Listening for PHP Debug Connection` in your PHPStorm and execute `magedev tests:debug` in your terminal.

# Executing bin/magento commands

Magedev provides a task to run any magento console command (bin/magento).

    magento:command <command>    Execute command with bin/magento

The placeholder `<command>` can be any console command, that exists in magento, e.g.: `index:reindex` or `sampledata:deploy`
