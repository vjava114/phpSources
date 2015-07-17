#!/bin/bash
cd /www/tomtom/batch
/usr/local/bin/php sendmsg.php >> /tmp/sendlog.log &
