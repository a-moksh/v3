#
# amazonlinux php7.3 FPM
#
FROM amazonlinux:2
MAINTAINER tomotaka777

# update yum
RUN yum update -y && yum clean all

#install ImageMagick
RUN yum -y install ImageMagick ImageMagick-devel

#install php7.3
RUN amazon-linux-extras install php8.1 -y
RUN yum -y install php-opcache php-apcu php-phpunit-PHPUnit php-mbstring php-xml \
            php-bcmath php-gd php-imagick php-intl php-mbstring php-mcrypt php-oauth \
            php-odbc php-soap php-uuid php-xml php-zip php-zlib php-redis php-pear php-devel

# install composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# other command install
RUN yum -y install zip unzip vim gcc make

# Install xdebug
RUN pecl install xdebug
RUN echo "[xdebug]" >> /etc/php.ini
RUN echo "zend_extension=\"/usr/lib64/php/modules/xdebug.so\"" >> /etc/php.ini
RUN echo "xdebug.remote_enable=1" >> /etc/php.ini
RUN echo "xdebug.remote_autostart=1" >> /etc/php.ini
RUN echo "xdebug.remote_connect_back=Off" >> /etc/php.ini
RUN echo "xdebug.remote_host=docker.for.mac.localhost" >> /etc/php.ini

#set timezone and locale
RUN cp -p /usr/share/zoneinfo/Japan /etc/localtime
COPY conf/clock /etc/sysconfig/clock
RUN echo "LANG=ja_JP.UTF-8" > /etc/sysconfig/i18n
RUN sed -i -e "s|^;date.timezone =.*$|date.timezone = Asia/Tokyo|" /etc/php.ini

# config php-fpm
RUN sed -i -e "s|^listen =.*$|listen = 9000|" /etc/php-fpm.d/www.conf
RUN sed -i -e "s|^listen\.allowed_clients =.*$|;listen.allowed_clients = |" /etc/php-fpm.d/www.conf

EXPOSE 9000
CMD ["/usr/sbin/php-fpm", "-F"]
