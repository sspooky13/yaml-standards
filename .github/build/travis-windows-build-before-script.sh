# install php
choco install php --version ${PHP_VERSION} --package-parameters='"/InstallDir:C:\php"'

# Export windows path into unix path
export PATH=/c/php:$PATH

# Enable extensions
sed -i 's/;extension=openssl/extension=openssl/g' /c/php/php.ini
sed -i 's/;extension=mbstring/extension=mbstring/g' /c/php/php.ini
sed -i 's/;extension=curl/extension=curl/g' /c/php/php.ini
# PHP 7.1 has name of extensions different as other higher versions
sed -i 's/;extension=php_openssl/extension=php_openssl/g' /c/php/php.ini
sed -i 's/;extension=php_mbstring/extension=php_mbstring/g' /c/php/php.ini
sed -i 's/;extension=php_curl/extension=php_curl/g' /c/php/php.ini

# Fetch composer
wget http://getcomposer.org/composer.phar

php composer.phar self-update

if [ "${DEPENDENCIES}" = "lowest" ]; then php composer.phar update --prefer-lowest --prefer-dist --no-interaction --no-progress; fi;
if [ "${DEPENDENCIES}" = "highest" ]; then php composer.phar update --prefer-dist --no-interaction --no-progress; fi;

vendor/bin/phing build-ci
