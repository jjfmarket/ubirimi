#!/usr/bin/env bash

mkdir temp_deploy
cd temp_deploy
git clone --depth 1 https://github.com/ubirimi/ubirimi.git
cp -r ubirimi /var/www/products/current/
cd /var/www/products/current/
curl -sS https://getcomposer.org/installer | php
php composer install


