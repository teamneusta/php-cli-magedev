# Varnish

    magedev docker:reinit


    {
      "magento_version": "2",
      "domain": "magento2.local",
      "docker": {
        "links": {
          "main": ["mysql", "redis", "elasticsearch"],
          "varnish": ["main"]
        },
        "ports": {
          "main": {
            "80": "8080"
          },
          "mysql": {
            "3306": "3306"
          },
          "varnish": {
            "80": "80"
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

Configuration -> System -> Full Page Cache and change Cache Application to Varnish Cache.

`.magedev/var/Docker/conf/varnish/etc/varnish/default.vcl`
