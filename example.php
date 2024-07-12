<?php
$location = realpath(dirname(__FILE__));
require_once $location . '/function.php';
$leftChannelArray = file_get_contents("{$location}/temporary/input-left.txt");
$leftChannelArray = explode(PHP_EOL, $leftChannelArray);
$rightChannelArray = file_get_contents("{$location}/temporary/input-right.txt");
$rightChannelArray = explode(PHP_EOL, $rightChannelArray);
$filePath = "{$location}/temporary/output.wav";
$return = createWavFile($leftChannelArray, $rightChannelArray, $filePath, 44100, 16);
var_dump($return);