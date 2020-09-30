#!/bin/sh

#php
cp oam/php/dev/config.ini oam/php/config/config.ini
cp oam/php/dev/Dockerfile oam/php/config/Dockerfile

#nginx
cp oam/nginx/dev/nginx_admin.conf oam/nginx/config/nginx_admin.conf
cp oam/nginx/dev/nginx_portal.conf oam/nginx/config/nginx_portal.conf
