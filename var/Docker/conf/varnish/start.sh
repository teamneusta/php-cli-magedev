#!/bin/bash

# Start varnish and log
# varnishd -f /etc/varnish/default.vcl -s malloc,100M -a 0.0.0.0:6082 -b 0.0.0.0:80
varnishd -f /etc/varnish/default.vcl -s malloc,100M
# varnishlog
