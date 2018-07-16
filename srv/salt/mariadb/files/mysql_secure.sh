#!/bin/bash

#
# Config
#

ROOT_PASS='{{ pillar['mysql']['root_pass'] }}'
APP_DB='{{ pillar['mysql']['app_db'] }}'
APP_USER='{{ pillar['mysql']['app_user'] }}'
APP_PASS='{{ pillar['mysql']['app_pass'] }}'

#
# Initial setup
#

RESULT=`mysqlshow --user=root --password=${ROOT_PASS} ${APP_DB}| grep -v Wildcard | grep -o ${APP_DB}`
if [ "$RESULT" != "Unknown database '${APP_DB}'" ]; then

    # Create root user
    mysqladmin -u root password "${ROOT_PASS}"

    # Kill the anonymous users
    echo "DROP USER ''@'localhost'" | mysql -uroot -p${ROOT_PASS}
    # Because our hostname varies we'll use some Bash magic here.
    echo "DROP USER ''@'$(hostname)'" | mysql -uroot -p${ROOT_PASS}
    # Kill off the demo database
    echo "DROP DATABASE test" | mysql -uroot -p${ROOT_PASS}
    # Make our changes take effect
    echo "FLUSH PRIVILEGES" | mysql -uroot -p${ROOT_PASS}

    #
    # Create database and new app user
    #

    echo "CREATE DATABASE ${APP_DB};" | mysql -uroot -p${ROOT_PASS}
    echo "CREATE USER '${APP_USER}'@'localhost' IDENTIFIED BY '${APP_PASS}';" | mysql -uroot -p${ROOT_PASS}
    echo "CREATE USER '${APP_USER}'@'192.168.%' IDENTIFIED BY '${APP_PASS}';" | mysql -uroot -p${ROOT_PASS}
    echo "CREATE USER '${APP_USER}'@'10.%' IDENTIFIED BY '${APP_PASS}';" | mysql -uroot -p${ROOT_PASS}
    echo "GRANT ALL PRIVILEGES ON ${APP_DB}.* TO '${APP_USER}'@'localhost';" | mysql -uroot -p${ROOT_PASS}
    echo "GRANT ALL PRIVILEGES ON ${APP_DB}.* TO '${APP_USER}'@'192.168.%';" | mysql -uroot -p${ROOT_PASS}
    echo "GRANT ALL PRIVILEGES ON ${APP_DB}.* TO '${APP_USER}'@'10.%';" | mysql -uroot -p${ROOT_PASS}
    echo "FLUSH PRIVILEGES;" | mysql -uroot -p${ROOT_PASS}

fi


