FROM php:7.1-fpm-buster as base

ARG project_root=.

# install required tools
# git for computing diffs
# wget for installation of other tools
# locales for locale-gen command
# apt-utils so package configuartion does not get delayed
# unzip to ommit composer zip packages corruption
# dialog for apt-get to be
RUN apt-get update && apt-get install -y git wget locales apt-utils unzip dialog && apt-get clean

# install Composer
COPY ${project_root}/docker/docker-install-composer /usr/local/bin/docker-install-composer

RUN chmod +x /usr/local/bin/docker-install-composer && \
    docker-install-composer

RUN apt-get update && \
    apt-get install -y \
    bash-completion \
    vim \
    nano \
    mc \
    htop && \
    apt-get clean

RUN docker-php-ext-install \
    opcache

# install locales and switch to en_US.utf8 in order to enable UTF-8 support
# see http://jaredmarkell.com/docker-and-locales/ from where was this code taken
RUN sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen && \
    locale-gen
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

# copy php.ini configuration
COPY ${project_root}/docker/php-ini-overrides.ini /usr/local/etc/php/php.ini

# overwrite the original entry-point from the PHP Docker image with our own
COPY ${project_root}/docker/docker-php-entrypoint /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-php-entrypoint

# set www-data user his home directory
# the user "www-data" is used when running the image, and therefore should own the workdir
RUN usermod -m -d /home/www-data www-data && \
    mkdir -p /var/www/html && \
    chown -R www-data:www-data /home/www-data /var/www/html

# Switch to user
USER www-data

# enable bash completion
RUN echo "source /etc/bash_completion" >> ~/.bashrc

# set COMPOSER_MEMORY_LIMIT to -1 (i.e. unlimited - this is a hotfix until https://github.com/shopsys/shopsys/issues/634 is solved)
ENV COMPOSER_MEMORY_LIMIT=-1

USER root

# allow overwriting UID and GID o the user "www-data" to help solve issues with permissions in mounted volumes
# if the GID is already in use, we will assign GID 33 instead (33 is the standard uid/gid for "www-data" in Debian)
ARG www_data_uid
ARG www_data_gid
RUN if [ -n "$www_data_uid" ]; then deluser www-data && (addgroup --gid $www_data_gid www-data || addgroup --gid 33 www-data) && adduser --system --home /home/www-data --uid $www_data_uid --disabled-password --group www-data; fi;

# as the UID and GID might have changed, change the ownership of the home directory workdir again
RUN chown -R www-data:www-data /home/www-data /var/www/html

USER www-data
