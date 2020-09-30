#!/bin/sh

#php
cp oam/php/prod/config.ini oam/php/config/config.ini
cp oam/php/prod/Dockerfile oam/php/config/Dockerfile

#nginx
cp oam/nginx/prod/nginx_admin.conf oam/nginx/config/nginx_admin.conf
cp oam/nginx/prod/nginx_portal.conf oam/nginx/config/nginx_portal.conf