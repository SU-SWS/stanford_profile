# Dev container for stanford_profile: PHP 8.4 with Apache, plus what Drupal
# and the profile need. build-site.sh builds a Drupal site around this
# repository in /var/www/site.
FROM mcr.microsoft.com/devcontainers/php:8.4

# PHP extensions Drupal and the profile need (it requires imagick),
# ImageMagick (the profile's image toolkit), rsync (the site build) and a
# MySQL client for drush sql-cli and sql-dump.
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    default-mysql-client \
    imagemagick \
    libbz2-dev \
    libfreetype-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libmagickwand-dev \
    libpng-dev \
    libzip-dev \
    rsync \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j"$(nproc)" bz2 calendar gd intl mysqli pdo_mysql zip \
  && printf '\n' | pecl install imagick \
  && docker-php-ext-enable imagick \
  && rm -rf /var/lib/apt/lists/*

# The MariaDB client verifies the server's certificate by default, and the
# MySQL container's is self-signed.
RUN mkdir -p /etc/mysql/conf.d \
  && printf '[client]\nskip-ssl-verify-server-cert\n' > /etc/mysql/conf.d/skip-ssl-verify.cnf

# PHP settings for development. Xdebug is installed but only starts when asked
# (XDEBUG_TRIGGER, or a browser extension), so Drush and page loads stay fast.
RUN { \
    echo 'memory_limit = -1'; \
    echo 'upload_max_filesize = 64M'; \
    echo 'post_max_size = 64M'; \
    echo 'max_execution_time = 300'; \
    echo 'error_reporting = E_ALL & ~E_DEPRECATED'; \
  } > /usr/local/etc/php/conf.d/zz-stanford-profile.ini \
  && sed -i 's/^xdebug.start_with_request = yes/xdebug.start_with_request = trigger/' /usr/local/etc/php/conf.d/*xdebug*.ini

# The site lives in a volume at /var/www/site (see docker-compose.yml). Apache
# serves its web directory, with .htaccess rules, as the vscode user so the web
# server and Drush can both write files.
RUN mkdir -p /var/www/site && chown vscode:vscode /var/www/site \
  && a2enmod rewrite \
  && sed -i 's#DocumentRoot /var/www/html$#DocumentRoot /var/www/site/drupal/web#' /etc/apache2/sites-available/000-default.conf \
  && printf '<Directory /var/www/site/drupal/web>\n  AllowOverride All\n  Require all granted\n</Directory>\n' > /etc/apache2/conf-available/site.conf \
  && a2enconf site \
  && sed -i 's/APACHE_RUN_USER:=www-data/APACHE_RUN_USER:=vscode/; s/APACHE_RUN_GROUP:=www-data/APACHE_RUN_GROUP:=vscode/' /etc/apache2/envvars \
  && echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
  && a2enconf servername
