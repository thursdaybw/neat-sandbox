<?php

namespace AiLife\GameLoops;

use AiLife\Dudes\Dude;
use Nawarian\Raylib\Types\Color;

use Nawarian\Raylib\Types\Rectangle;
use Nawarian\Raylib\Types\Vector2;

use FANNConnection;

use function Nawarian\Raylib\{
  BeginDrawing,
  ClearBackground,
  CloseWindow,
  DrawText,
  DrawLine,
  DrawRectangle,
  DrawCircleLines,
  DrawCircle,
  EndDrawing,
  InitWindow,
  WindowShouldClose,
  CheckCollisionPointRec,
  CheckCollisionRecs
};

final class GameLoop {

  public $lowerBound;

  private int $survivor_count = 0;

  private mixed $minDistance = 0;

  private int $generationCount;

  private function remapRange($intValue, $oMin, $oMax, $nMin, $nMax) {
    // Range check
    if ($oMin == $oMax) {
      return false;
    }

    if ($nMin == $nMax) {
      return false;
    }

    // Check reversed input range
    $bReverseInput = false;
    $intOldMin = min($oMin, $oMax);
    $intOldMax = max($oMin, $oMax);
    if ($intOldMin != $oMin) {
      $bReverseInput = true;
    }

    // Check reversed output range
    $bReverseOutput = false;
    $intNewMin = min($nMin, $nMax);
    $intNewMax = max($nMin, $nMax);
    if ($intNewMin != $nMin) {
      $bReverseOutput = true;
    }

    $fRatio = ($intValue - $intOldMin) * ($intNewMax - $intNewMin) / ($intOldMax - $intOldMin);
    if ($bReverseInput) {
      $fRatio = ($intOldMax - $intValue) * ($intNewMax - $intNewMin) / ($intOldMax - $intOldMin);
    }

    $fResult = $fRatio + $intNewMin;
    if ($bReverseOutput) {
      $fResult = $intNewMax - $fRatio;
    }

    return $fResult;
  }

  function isInRectangle($dude) {
     $vector = new Vector2($dude->state['pos']['x'],$dude->state['pos']['y']);
     $rectangle = new Rectangle($this->leftBound, $this->lowerBound, $this->boxWidth,$this->boxHeight);


    return CheckCollisionPointRec($vector, $rectangle);
   }

  public function __construct(int $width, int $height, int $number_of_dudes, $lifespan, $nowindow = FALSE, $start_pos = [400, 500]) {

    $this->width = $width;
    $this->height = $height;
    $this->number_of_dudes = $number_of_dudes;
    $this->dudes = [];
    $this->lifespan = $lifespan;
    $this->generations = 1000;


    $this->leftBound = 0;
    $this->lowerBound = 0;
    $this->boxPhase = 2;

    $this->boxWidth = 275;
    $this->boxHeight = 220;
    $this->nowindow = $nowindow;
    $this->pos = $start_pos;

  }

  function getRandomWeightedElement(array $weightedValues) {
    $rand = mt_rand(1, (int) array_sum($weightedValues));

    foreach ($weightedValues as $key => $value) {
      $rand -= $value;
      if ($rand <= 0) {
        return $key;
      }
    }
 }

  private function randomMutationAmount() {
    //return random_float(-0.00000000000000001,0.000000000000000001);
    //return random_float(-0.000000000000001,0.0000000000000001);
    //random_float(-0.000000001,0.0000000001);
    //return random_float(-0.0000001,0.00000001);
    //return random_float(-0.001,0.001);
    return random_float(-0.5,0.5);
    //return random_float(-1.5,1.5);
  }

  private function distance($x1, $y1, $x2, $y2) {
    // Calculating distance
    return sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2) * 1.0);
  }

  public function drawTarget() {
    $this->leftBound = random_int(0, 800 - $this->boxWidth);
    $this->lowerBound = random_int(0, 600 - $this->boxHeight);

    $mid = new Rectangle(300, 200, 200, 150);
    $rectangle = new Rectangle($this->leftBound, $this->lowerBound, $this->boxWidth,$this->boxHeight);

    if (CheckCollisionRecs($mid, $rectangle)) {
      $this->drawTarget();
    }
    DrawCircleLines($this->midpoint_x, $this->midpoint_y, 2, Color::red());
  }

  public function start(): void {

    if(!$this->nowindow) {
      InitWindow(800, 600, 'My raylib Window using FFI');
    }

    $this->generationCount = 0;

    while (TRUE) {
      echo "Start loop generation count: $this->generationCount\n";
//      $this->pos = [
//        'x' => random_int(0,800),
//        'y' => random_int(0,600),
//      ];

      // Let this generation live.
      $results = $this->generation();
      $winners = &$results['winners'];
      $this->generationCount++;

      $this->drawTarget();

      $surviving_dudes = count($this->dudes);
      echo "Surviving dudes: $surviving_dudes, having babies.\n";

      foreach ($this->dudes as $dude_id => $dude) {
        //echo "In dudes foreach\n";
        $dude->state['pos']['x'] = $this->pos['x'];
        $dude->state['pos']['y'] = $this->pos['y'];
        $dude->state['age']      = 0;
        $dude->state['color']    = 'maroon';

	if ($dude_id == $results['firstMan']) {
          $dude->state['color'] = 'orange';
	}
      }

      if (!empty($this->dudes) && !empty($winners)) {
        //print_r($winners);
        while (count($this->dudes) < $this->number_of_dudes) {
          foreach ($winners as $winner) {
            if (count($this->dudes) < $this->number_of_dudes) {
              //echo "New bred dude\n";
              $this->dudes[] = $this->newBredDude($this->dudes[$winner]);
            }
            else {
              break;
            }
          }
        }
      }

      echo "New count " . count($this->dudes) . "\n";

    }

    CloseWindow();
  }

  public function normalize($value, $min, $max) {
    $normalized = ($value - $min) / ($max - $min);
      return $normalized;
  }

  public function reverse_normalize($value, $min, $max) {
    //$normalized = ($value - $min) / ($max - $min);
    $normalized = $max - $value / $max - $min;
    return $normalized;
  }

  public function generation() {
    $life_count = 0;

    // Birth some random dudes to bring the population up.
    $count = 0;
    if (empty($this->dudes)) {
      echo "Dudes empty, build from scratch\n";
      $this->generationCount = 1;
      while (count($this->dudes) < $this->number_of_dudes) {
        $dude          = new Dude($this->pos, $count, $this);
        $this->dudes[] = $dude;
        $count++;
      }
    }

    $this->midpoint_x = $this->leftBound + ($this->boxWidth / 2);
    $this->midpoint_y = $this->lowerBound + $this->boxHeight - ($this->boxHeight / 2);

    echo "Dudes are alive and moving\n";
    while ($life_count <= $this->lifespan) {
      $this->updateState();
      if (!$this->nowindow) {
        $this->draw();
      }
      $life_count++;
    }
    echo "Dudes done moving.. check distances\n";
    echo "How many dudes right now?.. " . count($this->dudes) . "\n";

    $distances = [];
    foreach ($this->dudes as $dude_id => $dude) {
      $distances[$dude_id] = $this->distance($dude->state['pos']['x'], $dude->state['pos']['y'], $this->midpoint_x, $this->midpoint_y);// + $dude->steps;
    }

    $winners = [];
    asort($distances);

    $distances_remapped_1 = [];
    foreach ($distances as $dude_id => $distance) {

      if ($this->generationCount < 200) {
        //echo "Keeping dude!\n";
        $distances_remapped_1[$dude_id] = $this->remapRange($distance, min($distances), max($distances), 100, -100);
      }
      else {
        if ($this->isInRectangle($this->dudes[$dude_id]) && $this->generationCount > 5) {
          $distances_remapped_1[$dude_id] = $this->remapRange($distance, min($distances), max($distances), 100, -100);
        }
        else {
          unset($this->dudes[$dude_id]);
        }
      }

    }

    $firstMan = FALSE;
    if (!empty($distances_remapped_1)) {
      echo "percent: " . max($distances_remapped_1) . "\n";
      foreach ($distances_remapped_1 as $dude_id => $distance_percentage) {
        if (random_int(1, 100) <= ceil($distance_percentage)) {
          if ($firstMan === FALSE) {
	    $firstMan = $dude_id;
	  }
          //echo "Winner $dude_id: (chance was $distance_percentage%). {$distances[$dude_id]}.\n";
          $winners[] = $dude_id;
        }
      }
    }

//    if (empty($winners)) {
//      echo "Winners is empty, add one for fun\n";
//      $winners[] = max(array_keys($this->dudes));
//    }

    if (count($this->dudes) > 10) {
      $how_many_dudes_to_kill = floor(95 / 100 * count($distances));
      $longest_distances      = array_slice($distances, count($distances) - $how_many_dudes_to_kill, $how_many_dudes_to_kill, TRUE);
      foreach ($longest_distances as $dude_id => $distance) {
        if (!in_array($dude_id, $winners)) {
          unset($this->dudes[$dude_id]);
        }
      }
    }

    //echo "Min distance is " . min($distances) . "\n";
    if (!empty($distances)) {
      $this->minDistance = min($distances);
    }
    else {
      $this->minDistance = 0;
    }

    return [
     'firstMan' => $firstMan, 
     'winners'  => $winners,
    ];
  }

  public function updateState() {
    foreach ($this->dudes as $dude_id => $dude) {
      $this->dudes[$dude_id]->move();
    }
  }

  public function draw() {
    BeginDrawing();
    ClearBackground(Color::white());


    DrawRectangle($this->leftBound, $this->lowerBound, $this->boxWidth, $this->boxHeight, Color::skyBlue());
    DrawRectangle(300, 200, 200, 150, Color::orange());
    DrawCircleLines($this->midpoint_x, $this->midpoint_y, 2, Color::red());
    DrawCircleLines($this->midpoint_x, $this->midpoint_y, 3, Color::red());
    DrawCircleLines($this->midpoint_x, $this->midpoint_y, 4, Color::red());

    DrawCircleLines($this->midpoint_x, $this->midpoint_y, 7, Color::red());
    DrawCircleLines($this->midpoint_x, $this->midpoint_y, 8, Color::red());
    DrawCircleLines($this->midpoint_x, $this->midpoint_y, 9, Color::red());

    DrawCircleLines($this->midpoint_x, $this->midpoint_y, 12, Color::red());
    DrawCircleLines($this->midpoint_x, $this->midpoint_y, 13, Color::red());
    DrawCircleLines($this->midpoint_x, $this->midpoint_y, 14, Color::red());


    foreach ($this->dudes as $dude_id => $dude) {

      if ($dude->state['color'] == 'maroon') {
        $color = $dude->state['color'];
	$width = 5;
	$height = 5;
        //DrawRectangle($dude->state['pos']['x'], $dude->state['pos']['y'], $width, $height, Color::$color());
      }
      else if ($dude->state['color'] == 'orange') {
        DrawCircle($dude->state['pos']['x'], $dude->state['pos']['y'], 20, Color::violet());
      }
      
      $dude->state['count']++;
    }

    DrawText("Closest distance:  $this->minDistance", 0, 0,20, Color::darkGray());
    EndDrawing();
  }

 /**
 * @param mixed $dude
 * @return \AiLife\Dudes\Dude
 */
  private function newBredDude(Dude $dude): Dude {
    $new_dude = clone $dude;
    //$new_dude->ann = $dude->ann;
    //$new_dude->state = $dude->state;
    $new_dude->state['color'] = 'blue';
    $new_dude->steps = 0;

    $connection_array = fann_get_connection_array($new_dude->ann);
    $connection_key = array_rand($connection_array);
    $connection = $connection_array[$connection_key];
    $new_weight = $connection->getWeight() + $this->randomMutationAmount();

    fann_set_weight($new_dude->ann, $connection->getFromNeuron(), $connection->getToNeuron(), $new_weight);

    return $new_dude;
  }

}
