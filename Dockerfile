# Utilitzem una imatge oficial amb Apache + PHP
FROM php:8.2-apache

# Activem el mòdul SSL i el mòdul de reescriptura (opcional)
RUN a2enmod ssl rewrite

# Copiem els arxius de la web al directori d'Apache
COPY src/ /var/www/html/

# Copiem els certificats
COPY certs/server.crt /etc/ssl/certs/server.crt
COPY certs/server.key /etc/ssl/private/server.key

# Configuració d'Apache amb HTTPS
RUN echo '<VirtualHost *:443>\n\
    ServerAdmin admin@perart.com\n\
    DocumentRoot /var/www/html\n\
    SSLEngine on\n\
    SSLCertificateFile /etc/ssl/certs/server.crt\n\
    SSLCertificateKeyFile /etc/ssl/private/server.key\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/default-ssl.conf && \
    a2ensite default-ssl

# Exposem el port HTTPS
EXPOSE 443
