# New Projects

## Magento2

    git clone https://github.com/magento/magento2.git && cd magento2

```
cat >magedev.json <<'EOL'
{
  "magento_version": "2",
  "domain": "magento2.local"
}
EOL
```
    magedev docker:start
    magedev magento:install
    curl magento2.local


## Magento1

    unzip magento-1.9.2.1-2015-08-03-06-33-36.zip && cd magento

```
cat >magedev.json <<'EOL'
{
  "magento_version": "1",
  "domain": "magento.local"
}
EOL
```
    magedev docker:start
    curl magento.local

# Existing Projects

Existing projects can have a database dump and a media archive which can be imported automatically by magedev. Usually, these will be placed on the network drive to distribute them easily.

Here is an example `magedev.json` file with sql dump and media archive placed inside the shared network folder:

    {
      "magento_version": "2",
      "source_folder": "Source/",
      "domain": "some-project.local",
      "dump_file": "~/smb/share/Projekte/Web-Projekte/magedev/some-project/dump.sql",
      "media_archive": "~/smb/share/Projekte/Web-Projekte/magedev/some-project/media.zip"
    }

For bootstrapping the existing project, use the `init:project` command:

    magedev init:project

For debugging reasons you may use `-v` option to see, whats happening:

    magedev init:project -v

This task will do a lot of things for your, while taking differences between Magento1 and Magento2 into account.

* composer install
* npm install
* set up permissions
* install magerun
* updates db credentials for database container
* makes sure backend route is /admin
* imports a database dump
* imports a media dump
* updates base_url
* runs schema upgrades
* assures a backend user admin/admin123 exists
* creates a customer for login with credentials magento@neusta.de/magento3@neusta.de
* adds a host entry to your /etc/hosts

If everything went fine, you may open your browser and visit `http://some-project.local` in this case. Learn more on how to prepare existing projects for magedev [here](prepare-existing-projects).