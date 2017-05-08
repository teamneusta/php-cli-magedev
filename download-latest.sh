#!/bin/sh

BASEURL="https://raw.githubusercontent.com/teamneusta/php-cli-magedev/master/releases"
LATEST=$(curl -LSs $BASEURL/magedev-latest.phar)
echo $LATEST
curl -o magedev $BASEURL/$LATEST && chmod +x magedev
chmod +x magedev
