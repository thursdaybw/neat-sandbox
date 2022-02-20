# neat-sandbox

This is a very rough, and my first ever implementation of a learning neural network using the NEAT algorithm.

It's all in PHP.

It makes use of two non-traditional PHP library. Raylib for graphics and FANN for Neural networks.

https://github.com/raysan5/raylib

https://github.com/nawarian/raylib-ffi

https://libfann.github.io/fann/docs/files/fann-h.html#FANN_Creation/Execution

As a drupal web developer, I've fallen on using familar tools and so this all works in lando https://docs.lando.dev/basics/

There is a dockerfile that builds the raylib and fann libraries.

## Requirements

This has only been tested on Ubuntu 21.04 

## Installation

Install lando as per lando docs.
clone this repo and cd to it's directly.
Run `lando start'.


## Running.

Just run

```
lando php neural.php
```

## Learn more about neural networks in php
https://www.youtube.com/watch?v=5bFxDsoNFzU


## Raylib notes and links

### Excellent video covering the status PHP game and the RAYLIB-PHP library.
https://www.youtube.com/watch?v=q1X_6TYd030

### The raylib-library
https://github.com/joseph-montanez/raylib-php/

This looks like an excellect project. The info in the readme, the youtube videos etc have great information.

This library needs to be compiled and linked into PHP, I was unable to get this library to compile and link correctly and fell back to using [raylib-ffi](https://github.com/nawarian/raylib-ffi) which is slower but achieved my demo purposes for now.


### RaylibFFI
https://github.com/nawarian/raylib-ffi

This is the library I used to communicate with raylib c library. It's php based library so is installed with composer.
simple, easy, slow.

### RaylibCPP
This PHP library appears to be stalled, but not much more than raylib-php is. I haven't looked into this one much as it appears less stable thatn raylib-php and raylib-ffi worked for me.

## PHP Neural nets links and notes

## PHP FANN Fast 

An excellent overview of the FANN libary in PHP and how to use it. This is essentially a digestible interpretation of the PHP documentation.
https://www.youtube.com/watch?v=5bFxDsoNFzU&t=155s


PHP Docs:
https://www.php.net/manual/en/book.fann.php

FANN overview:
https://github.com/libfann/fann
