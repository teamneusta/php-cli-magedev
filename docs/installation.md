# Installation

## Download as phar

Magedev is a command line tool. Download latest version with download script or grap it from the `releases` folder.

    curl -sSL https://raw.githubusercontent.com/teamneusta/php-cli-magedev/master/download-latest.sh | sh

Make sure magedev lies in your `PATH`, move it somewhere e.g. `~/bin`:

    mv magedev ~/bin

## Updating magedev

To update your copy of magedev, you may use the `self-update` command like this:

    magedev self-update

## Adding autocomplete

For adding autocomplete, add this line to your `.bashrc` or `.zshrc`:

    source <(magedev _completion --generate-hook)

## Docker Installation guide

### Ubuntu 16.04

    curl -sSL https://get.docker.com/ | sh
    sudo usermod -aG docker $(whoami)
    sudo reboot

## Using latest dev version

For using the latest dev version in this repository, first clone it and create a symlink for the executable `bin/magedev`:

    git clone https://github.com/teamneusta/php-cli-magedev.git && cd php-cli-magedev && git checkout develop
    composer install --no-dev
    ln -s $(pwd)/bin/magedev ~/bin/magedev
    magedev
