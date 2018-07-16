# php install
php:
  pkg:
    - installed
    - names:
      - php-fpm
      - php-mysql
      - php-mcrypt
      - php-cli
      - php-json
      - php-curl
      - php-mbstring
      - php-zip
      - php-xml
      - php-bcmath
      - php-gd
  service:
    - running
    - enable: True
    - name: php7.0-fpm
    - require:
      - pkg: php

php-fpm-conf:
  cmd.run:
    - name: sed -i 's/#cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' /etc/php/7.0/fpm/php.ini
    - require:
      - pkg: php-fpm

php-fpm-restart:
  cmd.run:
    - name: service php7.0-fpm restart
    - require:
      - cmd: php-fpm-conf

composer:
  cmd.run:
    - name: curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
    - require:
      - cmd: php-fpm-restart

