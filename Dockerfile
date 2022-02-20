FROM ubuntu:20.04

#https://github.com/raysan5/raylib/wiki/Working-on-GNU-Linux

RUN apt-get update 
RUN ln -fs /usr/share/zoneinfo/Australia/Sydney /etc/localtime
RUN echo "Australia/Sydney" > /etc/timezone && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends tzdata
RUN apt-get install -y zip wget vim telnet net-tools libfann-dev build-essential git cmake libasound2-dev mesa-common-dev libx11-dev libxrandr-dev libxi-dev xorg-dev libgl1-mesa-dev libglu1-mesa-dev mesa-utils
#RUN php8.1-xdebug php8.1-dev
#RUN apt-get install -y php-xdebug php-dev
RUN apt-get install -y php7.4-xdebug php7.4-dev

# Compile and install php-fann
RUN git clone --recursive https://github.com/bukka/php-fann.git && cd php-fann && phpize && ./configure --with-fann && make && make install
#RUN echo "extension=fann" >> /etc/php/8.1/cli/php.ini
RUN echo "extension=fann" >> /etc/php/7.4/cli/php.ini

# Compile and install external glfw.
RUN wget https://github.com/glfw/glfw/releases/download/3.3.3/glfw-3.3.3.zip && unzip glfw-3.3.3.zip && cd glfw-3.3.3 && cmake -DBUILD_SHARED_LIBS=ON && make install

# Compile and install raylib c library.
#RUN cd /opt && git clone https://github.com/raysan5/raylib.git raylib
#RUN cd /opt && git clone --branch 3.7.0 --depth 1 https://github.com/raysan5/raylib.git raylib
#RUN cd /opt && git clone --branch 3.5.0 --depth 1 https://github.com/raysan5/raylib.git raylib
RUN cd /opt && git clone --branch 2.6.0 --depth 1 https://github.com/raysan5/raylib.git raylib
RUN mkdir -p /opt/raylib/build && cd /opt/raylib/build && cmake -DBUILD_SHARED_LIBS=ON -DUSE_EXTERNAL_GLFW=ON .. && make && make install

#Compile and install raylib PHP bindings to c library.
RUN cd /opt/ && git clone https://github.com/joseph-montanez/raylib-php raylib-php && cd /opt/raylib-php && git reset --hard 67d299ef1860c2854959e0c52a10155abfae291c
#RUN cd /opt/ && git clone https://github.com/nawarian/raylib-php raylib-php
RUN cd /opt/raylib-php && phpize && ./configure && make


# Install composer
#RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/da0a3a5b5c1c9d50f1d0115e7008018597dd3803/web/installer -O - -q | php -- --quiet && mv composer.phar /usr/bin/composer

# go :)
CMD ["sleep","infinity"] 
#CMD ["bash"] 
