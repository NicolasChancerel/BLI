<?php

namespace Att\M2X;

use Att\M2X\M2X;
use Att\M2X\Device;

class Trigger extends Resource {

/**
 * REST path of the resource
 *
 * @var string
 */
  public static $path = ':parent_path/triggers';

/**
 * The parent resource that this stream belongs to
 *
 * @var Resource
 */
  public $parent = null;
/**
 * The Trigger resource properties
 *
 * @var array
 */
  protected static $properties = array(
    'name', 'stream', 'condition', 'value',
    'callback_url', 'status', 'send_location'
  );

/**
 * Disable the original GET factory
 *
 * @param M2X $client
 * @param string $id
 * @return void
 */
  public static function get($client, $id) {
    throw new \BadMethodCallException('Not implemented, use Trigger::getTrigger() instead.');
  }

/**
 * Disable the original POST factory
 *
 * @param M2X $client
 * @param string $id
 * @return void
 */
  public static function create($client, $data = array()) {
    throw new \BadMethodCallException('Not implemented, use Trigger::createTrigger() instead.');
  }

/**
 * Retrieves a single resource
 *
 * @param Resource $parent
 * @param string $id
 * @return Resource
 */
  public static function getTrigger(M2X $client, Resource $parent, $id) {
    $response = $client->get(str_replace(':parent_path', $parent->path(), static::$path) . '/' . $id);

    $class = get_called_class();
    return new $class($client, $parent, $response->json());
  }

/**
 * Create a trigger resource
 *
 * @param M2X $client
 * @param Resource $parent
 * @param array $data
 * @return Stream
 */
  public static function createTrigger(M2X $client, Resource $parent, $data) {
    $path = str_replace(':parent_path', $parent->path(), static::$path);
    $response = $client->post($path, $data);
    return new self($client, $parent, $response->json());
  }

/**
 * Create object from API data
 *
 * @param M2X $client
 * @param Device $device
 * @param array $data
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
    return $this->id;
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
 * Test the specified trigger by firing it with a fake value.
 * This method can be used by developers of client applications
 * to test the way their apps receive and handle M2X notifications.
 *
 * @return array
 */
  public function test() {
    $this->client->post($this->path() . '/test');
  }
}
