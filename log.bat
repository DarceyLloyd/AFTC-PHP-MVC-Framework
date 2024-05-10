@echo off
cls
echo NGINX LOGS
docker logs nginx_container
echo --------------------
echo PHP LOGS
docker logs mysql_container
echo --------------------