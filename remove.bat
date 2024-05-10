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

echo -
echo # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
echo -

start /B docker-compose down

REM Stop and remove all containers
for /f "tokens=*" %%i in ('docker container ls -aq') do docker container stop %%i & docker container rm %%i

REM Remove all images
for /f "tokens=*" %%i in ('docker image ls -aq') do docker image rm %%i

REM Remove all volumes
for /f "tokens=*" %%i in ('docker volume ls -q') do docker volume rm %%i

REM Remove networks
@REM docker network rm dev_backend dev_default dev_dev_backend
docker network prune -f


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



@REM pause
