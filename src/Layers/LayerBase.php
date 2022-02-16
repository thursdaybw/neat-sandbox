<?php

namespace AiLife\Layers;

use AiLife\Neurons\NeuronInterface;

abstract class LayerBase {

  public array $neurons = [];

  /**
   * @return \AiLife\Neurons\NeuronInterface|array
   */
  public function getNeurons(): NeuronInterface|array {
    return $this->neurons;
  }

  /**
   * @param \AiLife\Neurons\NeuronInterface $neuron
   */
  public function addNeuron(NeuronInterface $neuron): void {
    $this->neurons[] = $neuron;
  }

}
