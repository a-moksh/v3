#
# mariadb:10.4
#
FROM mariadb:10.4
MAINTAINER tomotaka777

RUN apt-get update \
    && apt-get -y install mariadb-plugin-mroonga \
    && apt-get -y install nano \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && ln -s /usr/share/mysql/mroonga/install.sql /docker-entrypoint-initdb.d

#
# copy and chmod cnf file
#

COPY conf/my.cnf /etc/mysql/
RUN chmod 644 /etc/mysql/my.cnf

COPY conf/.login.cnf /etc/mysql/
RUN chmod 644 /etc/mysql/.login.cnf

COPY init.d/0000_create_database.sql /docker-entrypoint-initdb.d/

#
# set timezone
#
RUN /bin/cp -f /usr/share/zoneinfo/Asia/Tokyo /etc/localtime
