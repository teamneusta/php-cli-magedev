# Creating database or media dumps from existing projects

Take db dumps with `magerun` or `mysqldump` like this:

    user@server:/srv/web/current$ bin/magerun db:dump

Media files can be put into an tar archive like this:

    tar cvzf media.tar.gz media

_Please note:_ only plain sql files supported, no compression.

# Sanitize and cleanup database dumps before distribution

Database dumps from staging or production systems may contain sensitive customer informations and are usually very large. Let magedev help you to clean them up. You may import a dump if you are having the containers running like this:

    magedev db:import ~/db_dumps/project/some-dumpfile.sql

To execute sql cleanup scripts provided by magedev use this:

    magedev db:cleanup

Finally, the dump task will create a new sql dump file in your current directory:

    magedev db:dump
    ls -lh dump.sql
    -rw-r--r-- 1 bkatzmarski www-data 66M Dez 30 13:21 dump.sql

You may now move the dump file somewhere and reference its location in your `magedev.json` to use it for `init:project`:

    mv dump.sql ~/dumps/projectX.sql

    {
      "magento_version": "2",
      "source_folder": "Source/",
      "domain": "some-project.local",
      "dump_file": "~/dumps/projectsX.sql"
    }
