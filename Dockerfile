FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2dismod mpm_event mpm_worker mpm_prefork || true \
    && rm -f /etc/apache2/mods-enabled/mpm_event.* \
              /etc/apache2/mods-enabled/mpm_worker.* \
              /etc/apache2/mods-enabled/mpm_prefork.* \
    && a2enmod mpm_prefork

COPY apache-start.sh /usr/local/bin/apache-start.sh

RUN chmod +x /usr/local/bin/apache-start.sh

CMD ["/usr/local/bin/apache-start.sh"]