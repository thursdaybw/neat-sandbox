<?php

namespace AiLife\Neurons;

class InputNeuron extends IoNeuronBase {

  // Each input needs to know how to get it's value.
  public function process() {
    return random_int(0,10);
  }

}
