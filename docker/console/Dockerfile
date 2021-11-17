###########################################
## Prepare base php image for magento:
## - install required php extensions
###########################################
FROM php:7.4-cli-alpine AS php7.4-cli-alpine-magento

# Install php extension installer tool
# See https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

# Install required PHP extensions
# See https://devdocs.magento.com/guides/v2.4/install-gde/prereq/php-settings.html#verify-installed-extensions
RUN install-php-extensions \
  bcmath \
  ctype \
  curl \
  dom \
  gd \
  hash \
  iconv \
  intl \
  mbstring \
  openssl \
  pdo_mysql \
  simplexml \
  soap \
  xsl \
  zip \
  sockets

# Install additional PHP extensions
RUN install-php-extensions \
  opcache \
  pcntl \
  redis \
  imagick

ENTRYPOINT ["sleep","infinity"]


#######################################
## Extend base php magento image:
## - install sendmail
## - override www-data user id
## - install tools (mysql-client, composer, etc)
#######################################
FROM php7.4-cli-alpine-magento AS php7.4-cli-alpine-magento-dev

# Install & configure mhsendmail
# See https://github.com/tkotosz/docker-mhsendmail
# See https://github.com/tkotosz/mhsendmail-wrapper
COPY --from=tkotosz/mhsendmail:0.2.0 /usr/bin/mhsendmail /usr/local/bin/mhsendmail
COPY --from=tkotosz/mhsendmail-wrapper:latest /usr/bin/mhsendmail-wrapper /usr/local/bin/send_mail
RUN echo 'sendmail_path = "/usr/local/bin/send_mail"' > /usr/local/etc/php/conf.d/send_mail.ini

# Create app user
ARG APP_USER_ID=1000
ARG APP_GROUP_ID=1000
RUN addgroup -g ${APP_GROUP_ID} appuser \
    && adduser -u ${APP_USER_ID} -G appuser -h /home/appuser -s /bin/sh -D appuser


# Install additional tools
RUN apk add --no-cache \
    # only sh available by default
    bash \
    # required to connect to mysql cli from the console container
    mysql-client \
    # required for patch applies
    patch \
    git \
    # required for prefer-source composer install
    openssh-client

# Configure bash promt
RUN echo 'export PS1="$(whoami)@$(hostname):$(pwd)$ "' >> /home/appuser/.bashrc

# Install composer
COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

# Install n98 magerun
RUN curl -O https://files.magerun.net/n98-magerun2.phar \
  && chmod +x ./n98-magerun2.phar \
  && mv ./n98-magerun2.phar /usr/local/bin/n98-magerun2 \
  && ln -s /usr/local/bin/n98-magerun2 /usr/local/bin/magerun2

# Add configurations
COPY root /

WORKDIR /app
