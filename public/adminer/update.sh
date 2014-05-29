#!/bin/sh
#############################################################################
###
### Update Adminer to latest version
###
### Run in cron once a day/week to be up-to-date
###
#############################################################################

path=$(dirname $0)

wget http://www.adminer.org/latest-mysql.php -qO $path/new

### Check if Adminer file was fetched correct

find $path -name new -not -empty -exec mv {} $path/adminer.php \;
