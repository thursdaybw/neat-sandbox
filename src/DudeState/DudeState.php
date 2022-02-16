<?php

namespace AiLife\DudeState;

use AiLife\Neurons\Neuron;

class DudeState {

  public int $numberOfNeurons;
  public int $age;
  public string $color;
  public int $count;
  public array $pos = ['x' => 0, 'y', 0];

  function __construct($pos, $id) {
    $this->id = $id;

    //$this->state= new DudeState();

    $this->state = [
      'number_of_neurons' => random_int(1,10),
      'age' => 0,
      'color' => 'red',
      'count' => 0,
      'pos' => ['x' => $pos['x'], 'y' => $pos['y']],
    ];

    // Input neurons
    $input_names = $this->getInputNames();
    $this->state['input_weights'] = [];
    foreach ($input_names as $input_name) {
      $this->state['input_weights'][$input_name]['weight'] = $this->randomMutationAmount();
      $this->state['input_weights'][$input_name]['positive'] = random_int(0,1);
    }

    // Layers
    $no_of_hidden_layers = 2;
    $this->hiddenLayers = [
      'layers' => [],
    ];
    for ($i = 0; $i < $no_of_hidden_layers; $i++) {
      $neurons = [];
      while (count($neurons) < $this->state['number_of_neurons']) {
        $n_id = count($neurons);
        $neurons[] = new Neuron($n_id,$this->randomMUtationAmount(), random_int(0,1));
      }
      $this->hiddenLayers['layers'][] = [
        'neurons' => $neurons,
      ];
    }

    $this->hiddenLayers['layers'][0]['output_biases'] = [];
    $this->hiddenLayers['layers'][0]['output_weights'] = [];
    foreach ($this->hiddenLayers['layers'][0]['neurons'] as $n_id => $neuron) {
      $this->hiddenLayers['layers'][0]['output_biases'][$n_id] = $this->randomMUtationAmount();
      $this->hiddenLayers['layers'][0]['output_weights'][$n_id] = $this->randomMUtationAmount();
    }

    foreach ($this->hiddenLayers['layers'][1]['neurons'] as $n_id => $neuron) {
        $this->hiddenLayers['layers'][1]['output_biases'][$n_id] = $this->randomMUtationAmount();
        $this->hiddenLayers['layers'][1]['output_weights'][$n_id] = $this->randomMUtationAmount();
    }

  }

  private function getInputNames() {
    return [
      'posx',
      'posy',
      'age',
      'safe_x',
      'safe_y',
      'safe_width',
      'safe_height',
      'hit_top',
      'hit_left',
      'hit_bottom',
      'hit_right',
    ];
  }

  private function getCollatedInputs($x, $y, $width, $height) {
    $inputs = [
      'posx' => [
        'value' => $this->state['pos']['x'],
        'weight' => $this->state['input_weights']['posx']['weight'],
        'positive' => $this->state['input_weights']['posx']['positive'],
      ],
      'posy' => [
        'value' => $this->state['pos']['y'],
        'weight' => $this->state['input_weights']['posy']['weight'],
        'positive' => $this->state['input_weights']['posy']['positive'],
      ],
      'age' => [
        'value' => $this->state['age'],
        'weight' => $this->state['input_weights']['age']['weight'],
        'positive' => $this->state['input_weights']['age']['positive'],
      ],
      'safe_x' => [
        'value' => $x,
        'weight' => $this->state['input_weights']['safe_x']['weight'],
        'positive' => $this->state['input_weights']['safe_x']['positive'],
      ],
      'safe_y' => [
        'value' => $y,
        'weight' => $this->state['input_weights']['safe_y']['weight'],
        'positive' => $this->state['input_weights']['safe_y']['positive'],
      ],
      'safe_width' => [
        'value' => $width,
        'weight' => $this->state['input_weights']['safe_width']['weight'],
        'positive' => $this->state['input_weights']['safe_width']['positive'],
      ],
      'safe_height' => [
        'value' => $height,
        'weight' => $this->state['input_weights']['safe_height']['weight'],
        'positive' => $this->state['input_weights']['safe_height']['positive'],
      ],
      'hit_top' => [
        'value' => $this->hitTop,
        'weight' => $this->state['input_weights']['hit_top']['weight'],
        'positive' => $this->state['input_weights']['hit_top']['positive'],
      ],
      'hit_bottom' => [
        'value' => $this->hitBottom,
        'weight' => $this->state['input_weights']['hit_bottom']['weight'],
        'positive' => $this->state['input_weights']['hit_bottom']['positive'],
      ],
      'hit_left' => [
        'value' => $this->hitLeft,
        'weight' => $this->state['input_weights']['hit_left']['weight'],
        'positive' => $this->state['input_weights']['hit_left']['positive'],
      ],
      'hit_right' => [
        'value' => $this->hitRight,
        'weight' => $this->state['input_weights']['hit_right']['weight'],
        'positive' => $this->state['input_weights']['hit_right']['positive'],
      ],
    ];
    return $inputs;
  }

  public function move($x, $y, $width, $height) {
    $this->state['age']++;
    $start = microtime(true);

    // Layer 1; // Gets inputs.
    foreach ($this->hiddenLayers['layers'][0]['neurons'] as $neuron) {
      $inputs = $this->getCollatedInputs($x, $y, $width, $height);
      $hidden_layer_outputs_1[] = $neuron->process($inputs);
    }
    // Layer 1; // Gets inputs.
    $hidden_layer_2_outputs = [];
    foreach ($this->hiddenLayers['layers'][1]['neurons'] as $neuron) {
      $hidden_layer_2_inputs = [];
      foreach ($hidden_layer_outputs_1 as $output_value) {
        $hidden_layer_2_inputs[] = [
          'value' => $output_value,
          'weight' => $output_value,
        ];
      }
      $out = $neuron->process($hidden_layer_2_inputs);
      $hidden_layer_2_outputs[] = $out;
    }

    // Get X
    $sum = 0;
    foreach ($hidden_layer_2_outputs as $key => $hidden_layer_output) {
      $layer_output_weight = $this->hiddenLayers["layers"][1]["output_weights"][$key];
      $layer_output_bias = $this->hiddenLayers["layers"][1]["output_biases"][$key];
      $sum += $hidden_layer_output * $layer_output_weight + $layer_output_bias;
    }
    $x = tanh($sum) * 100;
    // Get X Direction.
    $sum = 0;
    foreach ($hidden_layer_2_outputs as $key => $hidden_layer_output) {
      $layer_output_weight = $this->hiddenLayers["layers"][1]["output_weights"][$key];
      $layer_output_bias = $this->hiddenLayers["layers"][1]["output_biases"][$key];
      $sum += $hidden_layer_output * $layer_output_weight + $layer_output_bias;
    }
    $x_direction = tanh($sum) * 100;

    // Get Y
    $sum = 0;
    foreach ($hidden_layer_2_outputs as $key => $hidden_layer_output) {
      $layer_output_weight = $this->hiddenLayers["layers"][1]["output_weights"][$key];
      $layer_output_bias = $this->hiddenLayers["layers"][1]["output_biases"][$key];
      $sum += $hidden_layer_output * $layer_output_weight + $layer_output_bias;
    }

    // Get Y Direction
    $y = tanh($sum) * 100;
    $sum = 0;
    foreach ($hidden_layer_2_outputs as $key => $hidden_layer_output) {
      $layer_output_weight = $this->hiddenLayers["layers"][1]["output_weights"][$key];
      $layer_output_bias = $this->hiddenLayers["layers"][1]["output_biases"][$key];
      $sum += $hidden_layer_output * $layer_output_weight + $layer_output_bias;
    }
    $y_direction = tanh($sum) * 100;

    $this->actionMoveX($x, $x_direction);
    $this->actionMoveY($y, $y_direction);

    $this->processing_time = microtime(true) - $start;
  }

  public function actionMoveX($output, $direction) {
    if ($this->id == 0) {
      //echo "x-direction $direction\n";
    }

    if (!($this->state['pos']['x'] > 190 && $this->state['pos']['x'] < 210) || !($this->state['pos']['y'] > 150 && $this->state['pos']['y'] < 350)) {

      $new_pos = $this->state['pos']['x'] + $output;

      if ($new_pos < 0) {
        $this->hitLeft = TRUE;
      }
      if ($new_pos > 800) {
        $this->hitRight = TRUE;
      }

      if ($new_pos > 0 && $new_pos < 800) {
        if ($direction > 1) {
          $this->state['pos']['x'] += $output;
        }
        else {
          $this->state['pos']['x'] -= $output;
        }
      }
    }
    else {
      //echo "hit the wall!\n";
    }
  }

  public function actionMoveY($output, $direction) {
    if ($this->id == 0) {
      //echo "y-direction $direction\n";
    }
    $new_pos = $this->state['pos']['y'] + $output;

    if ($new_pos < 0) {
      $this->hitTop = TRUE;
    }
    if ($new_pos > 600) {
      $this->hitBottom = TRUE;
    }


    if ($new_pos > 0 && $new_pos < 600) {
      if ($direction > 1) {
        $this->state['pos']['y'] += $output;
      }
      else {
        $this->state['pos']['y'] -= $output;
      }
    }
  }


}
