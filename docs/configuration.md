# Configuration

Magedev expects to find a config file `magedev.json` inside your project folder. Magedev can only operate if your current working directory contains this file. A very minimal configuration looks like this:

    {
      "magento_version": "2",
      "domain": "magento2.local"
    }

# Specify the source path

Sometimes, all project files for magento are in a subdirectory of the repository. In this case, you may specify the `source_folder` as a relative subdirectory.

    {
      "magento_version": "2",
      "domain": "magento2.local",
      "source_folder": "Source/",
    }

# Dump files

The command `init:project` is able to import a given database dump and a media archive for assets like product images. You may specify these as follows. Find out more about [bootstrap projects](bootstrap-projects.md).

    {
      "magento_version": "2",
      "domain": "magento2.local",
      "source_folder": "Source/",
      "dump_file": "~/smb/share/project/db.sql",
      "media_archive": "~/smb/share/project/media.tar.gz"
    }

# Proxy settings

If you are behind a corperate proxy and have no direct internet connection, you need to specify your proxy settings. First make sure, you have proxy settings for docker setup correctly. Otherwise, within you will not be able to download docker images. This is usally done by adding these lines to your `/etc/default/docker` config and restarting the service with `sudo service docker restart`.

    DOCKER_OPTS="--dns 172.31.64.4 --dns 172.31.64.5"

Tell magedev about the proxy in your config and it will use it when accessing external dependencies with `apt-get`, `composer` and `grunt`.

    {
      "magento_version": "2",
      "domain": "magento2.local",
      "proxy": {
        "HTTP": "http://yourproxy.de:3221",
        "HTTPS": "https://yourproxy.de:3222"
      }
    }

# Apply changes

Whenever you make changes to your configuration, you need to rebuild the containers. There is a short-hand command for this:

    magedev docker:reinit

# Local and global configuration file

Additionally to the `magedev.json` in your project, you may also place a global configuration file in your home folder
`~/.magedev.json`. This is useful to configure global settings like `proxy`. These files will be merged at runtime, where your project config has precedence.

# Full example of all available options

    {
      "magento_version": "2",
      "source_folder": "Source/",
      "domain": "ospig.local",
      "dump_file": "~/smb/share/project/db.sql",
      "media_archive": "~/smb/share/project/media.tar.gz",
      "proxy": {
        "HTTP": "http://yourproxy.de:3221",
        "HTTPS": "https://yourproxy.de:3222"
      },
      "phpunitxml_path": "/var/www/html/Source/Magento/bin/phpunit.xml",
      "users": {
        "admin": {
          "user": "admin",
          "email": "admin@localhost.de",
          "password": "admin123",
          "firstname": "admin",
          "lastname": "admin"
         },
         "customer": {
            "email": "test@example.com",
            "password": "test3@example.com",
            "firstname": "Test",
            "lastname": "Test"
         }
      },
      "xdebug": {
        "idekey": "vim",
        "remote_host": "172.17.0.1"
      },
      "docker": {
        "links": {
          "main": ["mysql", "redis", "elasticsearch"]
        },
        "ports": {
          "main": {
            "80": "80"
          },
          "mysql": {
            "3306": "3306"
          },
          "elasticsearch": {
            "9200": "9200",
            "9300": "9300"
          },
          "mailcatcher": {
            "1080": "1080",
            "1025": "1025"
          }
        }
      }
    }
