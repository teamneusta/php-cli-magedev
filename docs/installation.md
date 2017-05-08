# Installation

## Download as phar

Magedev is a command line tool. Download latest version with:

    curl -o magedev https://raw.githubusercontent.com/teamneusta/php-cli-magedev/master/releases/magedev-latest.phar && chmod +x magedev

Make sure magedev lies in your `PATH`, move it somewhere e.g. `~/bin`:

    mv magedev ~/bin

## Updating magedev

To update your copy of magedev, you may use the `self-update` command like this:

    magedev self-update

## Adding autocomplete

For adding autocomplete, add this line to your `.bashrc` or `.zshrc`:

    source <(magedev _completion --generate-hook)

## Using latest dev version

For using the latest dev version in this repository, first clone it and create a symlink for the executable `bin/magedev`:

    git clone https://github.com/teamneusta/php-cli-magedev.git && cd php-cli-magedev
    ln -s $(pwd)/bin/magedev ~/bin/magedev
    magedev
