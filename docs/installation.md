# Installation

Clone this repo and create a symlink of `magedev` into your local bin folder. From now on, you can use `magedev` in your terminal.

    git clone https://github.com/teamneusta/magedev.git && cd magedev
    ln -s $(pwd)/bin/magedev ~/bin/magedev
    magedev

## Adding autocomplete

For adding autocomplete, add this line to your `.bashrc` or `.zshrc`:

    source <(magedev _completion --generate-hook)

## Updating magedev

This one is easy. Just switch to your installation folder. If you don't know anymore, where the folder is, type this one and get a hint:

    ls -la $(which magedev)

Switch to this directory and simply update the repository with:

    git pull
