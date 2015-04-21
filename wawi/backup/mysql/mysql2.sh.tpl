#!/bin/bash
DBUSER=
DBPASSWORD=
DBLIVENAME=
DBBACKUPNAME=wawision_backup
DBSERVER=localhost

mount /dev/sdb1 /mnt
mysqldump -A -u${DBUSER} -p${DBPASSWORD}> /mnt/mysql/mysql_complete_`date +%y%m%d_%H%M`
rsync -rtv /var/www/ /mnt/
umount /mnt

mount /dev/sdc1 /mnt2
mysqldump -A -u${DBUSER} -p${DBPASSWORD}  > /mnt/mysql/mysql_complete_`date +%y%m%d_%H%M`
rsync -rtv /var/www/ /mnt/
umount /mnt

fCreateTable=""
fInsertData=""
echo "Copying database ... (may take a while ...)"
DBCONN="-h ${DBSERVER} -u ${DBUSER} --password=${DBPASSWORD}"
echo "DROP DATABASE IF EXISTS ${DBBACKUPNAME}" | mysql ${DBCONN}
echo "CREATE DATABASE ${DBBACKUPNAME}" | mysql ${DBCONN}
for TABLE in `echo "SHOW TABLES" | mysql $DBCONN $DBLIVENAME | tail -n +2`; do
        createTable=`echo "SHOW CREATE TABLE ${TABLE}"|mysql -B -r $DBCONN $DBLIVENAME|tail -n +2|cut -f 2-`
        fCreateTable="${fCreateTable} ; ${createTable}"
        insertData="INSERT INTO ${DBBACKUPNAME}.${TABLE} SELECT * FROM ${DBLIVENAME}.${TABLE}"
        fInsertData="${fInsertData} ; ${insertData}"
done;
echo "$fCreateTable ; $fInsertData" | mysql $DBCONN $DBBACKUPNAME
