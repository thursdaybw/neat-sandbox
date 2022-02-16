<?php

namespace AiLife\Neurons;

abstract class IoNeuronBase extends NeuronBase {

  public string $name;

  /**
   * @param string $name
   */
  public function __construct(string $name) {
    parent::__construct();
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }

}
