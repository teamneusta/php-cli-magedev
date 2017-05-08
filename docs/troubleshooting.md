# Troubleshooting

If you have problems start by appending `-v` to your command to see commands that are executed by magedev.

# Errors in docker

Sometimes you may encounter problems which are hidden inside the docker service.

    [Http\Client\Common\Exception\ClientErrorException (404)]
    Not Found

You may access more information in your journal like this.

    journalctl -u docker.service

# Known Issues

## Grunt is not killed

When using `magedev grunt:watch` and exiting with `ctrl+c` the process will remain active in the container. You cannot start watch again, it will fail with:

    Fatal error: Port 35729 is already in use by another process.

As a workaround, you may use `magedev grunt:kill` in this case, to stop the process and start again.

## Local services

This setup assumes you have no services like apache or mysql running on your host. All required services will be started inside containers and default ports will be forwarded to your host machine. Thats why your start command may fail with error like this:

    Error starting userland proxy: listen tcp 0.0.0.0:3306: bind: address already in use

Normally, magedev takes care to shut down these services if required. In case you have local services installed, you may use a bash alias in your `~/.bash_aliases` to stop them before

    alias stopall='service apache2 stop && service mysql stop'
