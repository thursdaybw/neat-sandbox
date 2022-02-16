<?php

namespace AiLife\Neurons;

use AiLife\Synapse\Synapse;

function sigmoid($t){
  return 1 / (1 + exp(-$t));
}

abstract class NeuronBase implements NeuronInterface {

  private float $bias;

  private string $activationFunction = "tanh";

  private Synapse $synapse;

  protected array $input_neurons;

  /**
   * @param $bias
   */
  public function __construct() {
    $this->uniqid = uniqid('',TRUE);
    $this->setBias($this->randomMutationAmount());
    $this->setActivationFunctionFromBool(random_int(0,1));
    $this->setSynapse(new Synapse());
  }

  public function setActivationFunctionFromBool(bool $bool) {
    if ($bool) {
      $this->activationFunction = "AiLife\Neurons\sigmoid";
    }
    else {
      $this->activationFunction = "tanh";
    }
  }

  /**
   * @return mixed
   */
  public function getBias() {
    return $this->bias;
  }

  /**
   * @param mixed $bias
   */
  public function setBias($bias): void {
    $this->bias = $bias;
  }

  /**
   * @return string
   */
  public function getActivationFunction(): string {
    return $this->activationFunction;
  }

  /**
   * @param string $activationFunction
   */
  public function setActivationFunction(string $activationFunction): void {
    $this->activationFunction = $activationFunction;
  }

  private function randomMutationAmount() {
    //return random_float(-0.00000000001, 0.00000000001);
    return random_float(-.1, .1);
  }


  /**
   * @return \AiLife\Synapse\Synapse
   */
  public function getSynapse(): Synapse {
    return $this->synapse;
  }

  /**
   * @param \AiLife\Synapse\Synapse $synapse
   */
  public function setSynapse(Synapse $synapse): void {
    $this->synapse = $synapse;
  }

  public function setInputs($inputs) {
    $this->input_neurons = $inputs;
  }

  public function process() {
    $sum = 0;
    foreach ($this->input_neurons as $input_neuron) {
      $input_value = $input_neuron->process();
      $synapse = $this->synapse;
      $weight = $synapse->weight;
      $sum = $sum + ($input_value * $weight);
    }

    if (!$this->synapse->positive_signum) {
      $sum *=-1;
    }

    $activation_function = $this->activationFunction;
    return $activation_function($sum);
  }

}
