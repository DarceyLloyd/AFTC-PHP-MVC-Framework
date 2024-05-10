@echo off

cls

docker ps -a
echo --------------------
docker images
echo --------------------
docker network ls
echo --------------------
docker volume ls
echo --------------------
docker-compose ps
echo --------------------