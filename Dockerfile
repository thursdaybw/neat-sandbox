FROM ubuntu:22.04
RUN apt-get update 
RUN ln -fs /usr/share/zoneinfo/Australia/Sydney /etc/localtime
RUN echo "Australia/Sydney" > /etc/timezone && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends tzdata
RUN apt -y install build-essential git cmake libasound2-dev mesa-common-dev libx11-dev libxrandr-dev libxi-dev xorg-dev libgl1-mesa-dev libglu1-mesa-dev
RUN apt -y install php8.1-dev
RUN cd /opt && git clone https://github.com/raysan5/raylib.git raylib
RUN mkdir -p /opt/raylib/build && cd /opt/raylib/build && cmake -DBUILD_SHARED_LIBS=ON .. && make && make install
RUN apt-get install -y wget vim php8.1-xdebug telnet net-tools libfann-dev
RUN git clone --recursive https://github.com/bukka/php-fann.git && cd php-fann && phpize && ./configure --with-fann && make && make install
RUN echo "extension=fann" >> /etc/php/8.1/cli/php.ini
CMD ["sleep","infinity"] 
#CMD ["bash"] 
