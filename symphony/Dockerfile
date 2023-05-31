FROM php:latest
RUN apt-get update && apt-get -y upgrade

RUN apt-get install -f -y curl
#RUN apt-get update && apt-get install -f -y curl

#RUN apt-get install lsb-release ca-certificates apt-transport-https software-properties-common -y
#RUN add-apt-repository -y ppa:ondrej/php
#RUN apt update
#RUN apt-get install -y php8.1

#php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"


RUN apt-get install -y sudo
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
RUN apt-get install -y symfony-cli 
RUN git config --global user.email "jeetendrag10@gmail.com"
RUN git config --global user.name "jeetendra"
ENTRYPOINT ["tail", "-f", "/dev/null"]


#FREQUENTLY USED TERMINAL COMMANDS FOR MANATIME PROJECT
#run container for manatime:             docker run -dit  -p 8000:8000 -v /home/jeetendra/Documents/manatime/symphony/symfony_app:/app manatime_symfony:latest
#build docker image for manatime :       docker build -t manatime_symfony:latest .
#new project(execute in /app folder):    symfony new manatime_project_directory --version="5.4.*" --webapp  
# test installation:                     symfony check:requirements
#get project info(exe in project folder):php bin/console about
#start php server(inside container):     symfony server:start

#Useful links
#https://symfony.com/doc/current/setup/file_permissions.html   #file permission problems