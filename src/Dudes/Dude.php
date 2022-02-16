<?php

namespace AiLife\Dudes;

use Nawarian\Raylib\Types\Rectangle;
use Nawarian\Raylib\Types\Vector2;

use function Nawarian\Raylib\{
  CheckCollisionPointRec,
};

class Dude {

  public $state;

  public $hiddenLayer;

  public $processing_time = 0;

  public $hitLeft = FALSE;

  public $hitRight = FALSE;

  public $hitTop = FALSE;

  public $hitBottom = FALSE;

  public $gameLoop;

  public $generation;

  public $steps = 0;

  private $inputLabels = [
    'posx',
    'posy',
    'age',
    'upperBound',
    'lowerBound',
    'leftBound',
    'rightBound',
    /*
    'safe_x',
    'safe_y',
    'safe_width',
    'safe_height',
    'hit_top',
    'hit_left',
    'hit_bottom',
    'hit_right',
    */
  ];

//  private $outputLabels = [
//    'distance_x',
//    'distance_y',
//    'direction_x',
//    'direction_y',
//  ];

  private $outputLabels = [
    'x_speed',
    'y_speed',
    'reversed',
  ];

  public string $state_serialized;

  /**
   * @var false|resource
   */
  public $ann;

  public function setPosX($x) {
    $this->state['pos']['x'] = $x;
    $this->state_serialized = serialize($this);
  }

  public function setPosY($y) {
    $this->state['pos']['y'] = $y;
    //$this->state_serialized = serialize($this->state);
  }

  public function setAge($age) {
    $this->state['age'] = $age;
    //$this->state_serialized = serialize($this->state);
  }

  public function setColor($color) {
    $this->state['color'] = $color;
    //$this->state_serialized = serialize($this->state);
  }

  public function setNumberOfNeurons($number_of_neurons) {
    $this->state['number_of_neurons'] = $number_of_neurons;
    //$this->state_serialized = serialize($this->state);
  }

  public function setNumberOfHiddenLayers($number_of_hidden_layers) {
    $this->state['number_of_hidden_layers'] = $number_of_hidden_layers;
    //$this->state_serialized = serialize($this->state);
  }

  private function randomMutationAmount() {
    return random_float(-.1, .1);
  }

  function __construct($pos, $id, $gameLoop) {
    $this->id = $id;
    $this->gameLoop = $gameLoop;
    $this->generation = 0;

    //$this->setNumberOfNeurons(random_int(0,20));
    //$this->setNumberOfHiddenLayers(random_int(0,20));
    $this->state = [
      'number_of_neurons' => random_int(1,50),
      'number_of_hidden_layers' => random_int(1,50),
      'age' => 0,
      'color' => 'beige',
      'count' => 0,
      'pos' => ['x' => $pos['x'], 'y' => $pos['y']],
    ];

    $layers = [
      count($this->inputLabels), // input layer
    ];

    // Hidden layers
    for ($i = 0; $i < $this->state['number_of_hidden_layers']; $i++) {
      // Input neurons
      $layers[] = random_int(1,15);
    }

    // Output layer
    $layers[] = count($this->outputLabels);

    $this->ann = fann_create_standard_array(count($layers), $layers);

    fann_set_activation_function_output($this->ann, FANN_SIGMOID_SYMMETRIC);
    $this->activation_function = random_int(0,17);
    fann_set_activation_function_hidden($this->ann, $this->activation_function);
    fann_randomize_weights($this->ann, -.2, .2);

  }

  public function move() {

    $inputs = [
      $this->state['pos']['x'],
      $this->state['pos']['y'],
      $this->state['age'],
      $this->gameLoop->leftBound,
      $this->gameLoop->lowerBound,
      $this->gameLoop->boxWidth,
      $this->gameLoop->boxHeight,
    ];

    $outputs = fann_run($this->ann, $inputs);

    $labelled_outputs = [];
    foreach ($outputs as $output) {
      $label = array_shift($this->outputLabels);
      $this->outputLabels[] = $label;
      $labelled_outputs[$label] = $output * 60;
    }
    $reversed = FALSE;
    if ($labelled_outputs['reversed'] > 0) {
      $reversed = TRUE;
    }

    $this->state['age']++;
    $start = microtime(true);

    $this->actionMove($labelled_outputs['x_speed'], $labelled_outputs['y_speed'], $reversed);

    $this->processing_time = microtime(true) - $start;

  }


  public function actionMove($x_speed, $y_speed, $reversed) {

    $dudes_vector = new Vector2($this->state['pos']['x'], $this->state['pos']['y']);
    $mid = new Rectangle(300, 200, 200, 150);

    if ($reversed) {
      $x_speed *=-1;
      $y_speed *=-1;
    }

    $new_pos_x = $this->state['pos']['x'] + $x_speed + random_int(0,1);
    $new_pos_y = $this->state['pos']['y'] + $y_speed + random_int(0,1);

    if (!CheckCollisionPointRec($dudes_vector,$mid)) {
      $this->state['pos']['x']  = $new_pos_x;
      $this->state['pos']['y']  = $new_pos_y;
    }

    $this->steps++;

  }

  public  function __clone() {
    $this->ann = fann_copy($this->ann);
  }

}
