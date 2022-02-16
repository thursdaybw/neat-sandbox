<?php

declare(strict_types=1);

use AiLife\GameLoops\GameLoop;

require_once './vendor/autoload.php';
require_once __DIR__ . '/vendor/nawarian/raylib-ffi/src/generated-functions.php';

global $bw_state;
$bw_start = 1;

function random_float ($min,$max) {
  //return random_int($min, $max) / $max;
  return ($min+lcg_value()*(abs($max-$min)));
}

$nowindow = FALSE;
if (isset($argv[1]) && $argv[1] === 'debug') {
  $nowindow = TRUE;
}

$start_pos = [
  'x' => random_int(0,800),
  'y' => random_int(0,600),
];
//$start_pos = [
//  'x' => 100,
//  'y' => 250,
//];
//$start_pos = [
//  'x' => 400,
//  'y' => 300,
//];
//
$loop = new GameLoop(800,600, 2000, 75, $nowindow, $start_pos);
$loop->start();
