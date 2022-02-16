<?php

namespace AiLife\Layers;

class HiddenLayer extends LayerBase {

  private int $neuronCeiling;

  public function __construct() {
    $this->neuronCeiling = random_int(2,5);
  }

  /**
   * @return int
   */
  public function getNeuronCeiling(): int {
    return $this->neuronCeiling;
  }

  /**
   * @param int $neuron_ceiling
   */
  public function setNeuronCeiling(int $neuron_ceiling): void {
    $this->neuronCeiling = $neuron_ceiling;
  }


}
