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
