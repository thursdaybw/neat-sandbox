<?php

namespace AiLife\Synapse;

class Synapse {

  public float $weight;
  public bool $positive_signum;

  /**
   */
  public function __construct() {
    $this->weight          = random_float(-0.1,1.0);
    $this->positive_signum = random_int(0,1);
  }

  /**
   * @return bool
   */
  public function isPositiveSignum(): bool {
    return $this->positive_signum;
  }

  /**
   * @param bool $positive_signum
   */
  public function setPositiveSignum(bool $positive_signum): void {
    $this->positive_signum = $positive_signum;
  }

  /**
   * @return float
   */
  public function getWeight(): float {
    return $this->weight;
  }

  /**
   * @param float $weight
   */
  public function setWeight(float $weight): void {
    $this->weight = $weight;
  }

}
