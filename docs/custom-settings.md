# Custom Settings for core_config_data

You may change some configuration values for the project according to your needs. Magedev will look up these paths inside your project directory:

  * `.magedev/var/data/magento2/config.yml`
  * `.magedev/var/data/magento1/config.yml`

This is an example `config.yml`:

    design/head/default_title: "Installed by Magedev"
    admin/security/password_lifetime: 9999999
    admin/security/password_is_forced: 0
    catalog/search/elasticsearch/servers: elasticsearch:9200
    admin/url/custom_path: admin
    dev/template/allow_symlink: 1
    web/secure/enable_upgrade_insecure: 0

These settings will be applied when executing `init:project` or `config:reset`.
