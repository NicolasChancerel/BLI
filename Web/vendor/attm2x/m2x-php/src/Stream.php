<?php

namespace Att\M2X;

use Att\M2X\M2X;
use Att\M2X\Device;

class Stream extends Resource {

/**
 * REST path of the resource
 *
 * @var string
 */
  public static $path = ':parent_path/streams';

/**
 * The parent resource that this stream belongs to
 *
 * @var Resource
 */
  public $parent = null;
/**
 * The Stream resource properties
 *
 * @var array
 */
  protected static $properties = array(
    'name', 'unit', 'type'
  );

/**
 * Disable the original GET factory
 *
 * @param M2X $client
 * @param string $id
 * @return void
 */
  public static function get($client, $id) {
    throw new \BadMethodCallException('Not implemented, use Stream::getStream() instead.');
  }

/**
 * Disable the original POST factory
 *
 * @param M2X $client
 * @param string $id
 * @return void
 */
  public static function create($client, $data = array()) {
    throw new \BadMethodCallException('Not implemented, use Stream::createStream() instead.');
  }

/**
 * Retrieves a single resource
 *
 * @param Resource $parent
 * @param string $id
 * @return Resource
 */
  public static function getStream(M2X $client, Resource $parent, $id) {
    $response = $client->get(str_replace(':parent_path', $parent->path(), static::$path) . '/' . $id);

    $class = get_called_class();
    return new $class($client, $parent, $response->json());
  }

/**
 * Create or update a stream resource
 *
 * @param M2X $client
 * @param Resource $parent
 * @param string $name
 * @param array $data
 * @return Stream
 */
  public static function createStream(M2X $client, Resource $parent, $name, $data) {
    $path = str_replace(':parent_path', $parent->path(), static::$path) . '/' . $name;
    $response = $client->put($path, $data);

    if ($response->statusCode == 204) {
      return self::getStream($client, $parent, $name);
    }

    return new self($client, $parent, $response->json());
  }

/**
 * Create object from API data
 *
 * @param M2X $client
 * @param Device $device
 * @param stdClass $data
 */
  public function __construct(M2X $client, Resource $parent, $data) {
    $this->parent = $parent;
    parent::__construct($client, $data);
  }

/**
 * The resource id for the REST URL
 *
 * @return string
 */
  public function id() {
    return $this->name;
  }

/**
 * Returns the path to the resource
 *
 * @return string
 */
  public function path() {
    return str_replace(':parent_path', $this->parent->path(), self::$path) . '/' . $this->id();
  }

/**
 * Update the current value of the stream. The timestamp is optional.
 * If ommited, the current server time will be used.
 *
 * @link https://m2x.att.com/developer/documentation/v2/device#Update-Data-Stream-Value
 *
 * @param string $value
 * @param string $timestamp
 * @return void
 */
  public function updateValue($value, $timestamp = null) {
    $data = array('value' => $value);

    if ($timestamp) {
      $data['timestamp'] = $timestamp;
    }

    $this->client->put($this->path() . '/value', $data);
  }

/**
 * List values from the stream, sorted in reverse chronological order
 * (most recent value first).
 *
 * @link https://m2x.att.com/developer/documentation/v2/device#List-Data-Stream-Values
 *
 * @param array $params
 * @return array
 */
  public function values($params = array()) {
    $response = $this->client->get($this->path() . '/values', $params);
    return $response->json();
  }

/**
 * Sample values from the stream, sorted in reverse chronological order
 * (most recent values first).
 *
 * This method only work for numeric streams
 *
 * @link https://m2x.att.com/developer/documentation/v2/device#Data-Stream-Sampling
 *
 * @param array $params
 * @return array
 */
  public function sampling($params = array()) {
    $response = $this->client->get($this->path() . '/sampling', $params);
    return $response->json();
  }

/**
 * Return count, min, max, average and standard deviation stats for the
 * values of the stream.
 *
 * This method only works for numeric stream
 *
 * @link https://m2x.att.com/developer/documentation/v2/device#Data-Stream-Stats
 *
 * @param array $params
 * @return array
 */
  public function stats($params = array()) {
    $response = $this->client->get($this->path() . '/stats', $params);
    return $response->json();
  }

/**
 * Post multiple values to the stream
 *
 * The `values` parameter is an array with the following format:
 *
 * array(
 *   array('timestamp' => <Time in ISO8601>, 'value' => x),
 *   array('timestamp' => <Time in ISO8601>, 'value' => y)
 * )
 *
 * https://m2x.att.com/developer/documentation/v2/device#Post-Data-Stream-Values
 *
 * @param array $data
 * @return void
 */
  public function postValues($values) {
    $data = array('values' => $values);
    $response = $this->client->post($this->path() . '/values', $data);
  }
}
