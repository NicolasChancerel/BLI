<?php

include '../vendor/autoload.php';

use Att\M2X\M2X;
use Att\M2X\Error\M2XException;

$api_key = "<API KEY HERE>";
$device_id = "<DEVICE>";
$stream  = "<STREAM NAME>";

$m2x = new M2X($api_key);

try {
  // View Device
  $device = $m2x->device($device_id);

  // Create/Update Stream
  $data = array(
    "type" => "numeric",
    "unit"  => array("label" => "Celsius")
  );
  $stream = $device->updateStream($stream, $data);

  // List Streams
  $streams = $device->streams();

  // Get Details From Existing Stream
  $stream = $device->stream($stream);

  // Read Values From Existing Stream
  $values = $stream->values();

  // Post Multiple Values To Stream
  $stream->postValues(array(
    array("value" => 456),
    array("value" => 789),
    array("value" => 123.145)
  ));

  // Read Location Information
  $info = $device->location();

  // Update Location Information
  $device->updateLocation(array(
    "name"      => "Seattle",
    "latitude"  => 47.6097,
    "longitude" => 122.3331
  ));
} catch (M2XException $ex) {
  echo $ex->getMessage();
  echo $ex->response->raw;
}