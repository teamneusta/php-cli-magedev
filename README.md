# Magedev

Magedev is a shell tool for local Magento development. It helps you to get a project up and running much easier by automating cumbersome tasks. Escpecially, Magento2 is not easy to setup and install all required dependencies.

## Experimental software

**Beware: this is experimental software still in heavy development. There might still be breaking changes. Use it on your own risk.**

## Requirements

* Linux
* PHP 5.6 or above
* docker 1.10 or above

Magedev uses Docker to orchestrate required services in containers. Make sure you have Docker installed on your system. You may use the official install script:

    wget -qO- https://get.docker.com/ | sh

## Installation

Clone this repo and create a symlink of `magedev` into your local bin folder. From now on, you can use `magedev` in your terminal.

    git clone https://github.com/teamneusta/php-cli-magedev.git && cd magedev
    ln -s $(pwd)/bin/magedev ~/bin/magedev
    magedev

## Updating magedev

This one is easy. Just switch to your installation folder. If you don't know anymore, where the folder is, type this one and get a hint:

    ls -la $(which magedev)

Switch to this directory and simply update the repository with:

    git pull

## Usage

See [the documentation](docs/index.md).

## Magento2 Installation at warp speed

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


## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.
