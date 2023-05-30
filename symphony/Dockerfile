FROM bitnami/symfony:5.4-debian-11
RUN apt-get update && apt-get install -f -y curl
#RUN sudo apt -y install apt-transport-https
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
RUN sudo apt-get install -y symfony-cli
RUN git config --global user.email "jeetendrag10@gmail.com"
RUN git config --global user.name "jeetendra"
ENTRYPOINT ["tail", "-f", "/dev/null"]


#FREQUENTLY USED TERMINAL COMMANDS FOR MANATIME PROJECT
#run container for manatime:             docker run -dit -v /home/jeetendra/Documents/manatime/symphony/symfony_app:/app manatime_symfony:latest
#build docker image for manatime :       docker build -t manatime_symfony:latest .
#new project(execute in /app folder):    symfony new manatime_project_directory --version="5.4.*" --webapp  #
# test installation:                     symfony check:requirements
#get project info(exe in project folder):php bin/console about