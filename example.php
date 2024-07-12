<?php
$location = realpath(dirname(__FILE__));
require_once $location . '/function.php';
$inputArray = file_get_contents("{$location}/temporary/input.txt");
$inputArray = explode(PHP_EOL, $inputArray);
$filePath = "{$location}/temporary/output.wav";
$return = createWavFile($inputArray, $filePath, 44100, 16);
var_dump($return);