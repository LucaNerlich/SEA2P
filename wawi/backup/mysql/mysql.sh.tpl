#!/bin/sh

mysqldump -A -uwawision -p | gzip > /var/www/backup/mysql/mysql_complete_`date +%y%m%d_%H%M`.gz

