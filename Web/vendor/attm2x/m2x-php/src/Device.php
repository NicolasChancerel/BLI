<?php

namespace Att\M2X;

use Att\M2X\Stream;
use Att\M2X\StreamCollection;
use Att\M2X\Trigger;
use Att\M2X\TriggerCollection;

class Device extends Resource {

/**
 * REST path of the resource
 *
 * @var string
 */
  public static $path = '/devices';

/**
 * The Key resource properties
 *
 * @var array
 */
  protected static $properties = array(
    'name', 'description', 'visibility', 'tags'
  );

/**
 * The resource id for the REST URL
 *
 * @return string
 */
  public function id() {
    return $this->id;
  }

/**
 * Get location details of the device, will return False if no 
 * location details are available. Otherwise it will return
 * an array with the details.
 *
 * @link https://m2x.att.com/developer/documentation/v2/device#Read-Device-Location
 *
 * @return array|boolean
 */
  public function location() {
    $response = $this->client->get(self::$path . '/' . $this->id . '/location');
    
    if ($response->statusCode == 204) {
      return False;
    }

    return $response->json();
  }

/**
 * Update the current location of the specified device.
 *
 * @link https://m2x.att.com/developer/documentation/v2/device#Update-Device-Location
 *
 * @param array $data
 * @return Device
 */
  public function updateLocation($data) {
    $response = $this->client->put(self::$path . '/' . $this->id . '/location', $data);
    return $this;
  }

/**
 * Retrieve list of data streams associated with the device
 *
 * @link https://m2x.att.com/developer/documentation/v2/device#List-Data-Streams
 *
 * @return StreamCollection
 */
  public function streams() {
    return new StreamCollection($this->client, $this);
  }

/**
 * Get details of a specific data Stream associated with the device
 *
 * @link https://m2x.att.com/developer/documentation/v2/device#View-Data-Stream
 *
 * @param string $name
 * @return Stream
 */
  public function stream($name) {
    return Stream::getStream($this->client, $this, $name);
  }

/**
 * Update a data stream associated with the Device, if a
 * stream with this name does not exist it gets created.
 *
 * @link https://m2x.att.com/developer/documentation/v2/device#Create-Update-Data-Stream
 *
 * @param string $name
 * @param array $data
 * @return Stream
 */
  public function updateStream($name, $data = array()) {
    return Stream::createStream($this->client, $this, $name, $data);
  }

/**
 * Post values to multiple streams for this device.
 *
 * The `values` parameter is an array with the following format:
 *
 * array(
 *   'stream_a' => array(
 *     array('timestamp' => <Time in ISO8601>, 'value' => x),
 *     array('timestamp' => <Time in ISO8601>, 'value' => y)
 *   ),
 *   'stream_b' => array(
 *     array('timestamp' => <Time in ISO8601>, 'value' => t),
 *     array('timestamp' => <Time in ISO8601>, 'value' => g)
 *   )
 * )
 * 
 * @link https://m2x.att.com/developer/documentation/v2/device#Post-Device-Updates--Multiple-Values-to-Multiple-Streams
 *
 * @param array $values
 * @return void
 */
  public function postUpdates($values) {
    $data = array('values' => $values);
    $response = $this->client->post($this->path() . '/updates', $data);
  }

/**
 * Retrieve list of triggers associated with the device
 *
 * @return TriggerCollection
 */
  public function triggers() {
    return new TriggerCollection($this->client, $this);
  }

/**
 * Get details of a specific trigger associated with the device
 *
 * @param string $id
 * @return Trigger
 */
  public function trigger($id) {
    return Trigger::getTrigger($this->client, $this, $id);
  }

/**
 * Create a trigger associated with the Device.
 *
 * @param array $data
 * @return Stream
 */
  public function createTrigger($data = array()) {
    return Trigger::createTrigger($this->client, $this, $data);
  }

/**
 * Retrieve list of HTTP requests received lately by the specified
 * device (up to 100 entries).
 *
 * @link https://m2x.att.com/developer/documentation/v2/device#View-Request-Log
 *
 * @return array
 */
  public function log() {
    $response = $this->client->get($this->path() . '/log');
    return current($response->json());
  }
}
