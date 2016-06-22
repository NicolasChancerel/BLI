<?php

namespace Att\M2X;

abstract class Resource {

/**
 * REST path of the resource
 *
 * @var string
 */
  public static $path = '';

/**
 * The resource properties
 *
 * @var array
 */
  protected static $properties = array();

/**
 * Holds the resource data properties
 *
 * @var array
 */
  protected $data = array();

/**
 * Holds the M2X Client instance
 *
 * @var M2X
 */
  protected $client;

/**
 * The primary identifier for the REST URL
 *
 * @return string
 */
  abstract public function id();

/**
 * Retrieves a list of resources
 *
 * @param M2X $client
 * @return array
 */
  public static function index($client) {
    $response = $client->get(static::$path);

    $data = (array) $response->json();

    $class = get_called_class();

    $resources = array();
    foreach (array_shift($data) as $resourceData) {
      $resources[] = new $class($client, $resourceData);
    }

    return $resources;
  }

/**
 * Retrieves a single resource
 *
 * @param M2X $client
 * @param string $id
 * @return Resource
 */
  public static function get($client, $id) {
    $response = $client->get(static::$path . '/' . $id);

    $class = get_called_class();
    return new $class($client, $response->json());
  }

/**
 * Creates a new resource
 *
 * @param M2X $client
 * @param array $data
 * @return Resource
 */
  public static function create($client, $data) {
    $response = $client->post(static::$path, $data);

    if ($response->statusCode == 201) {
      $class = get_called_class();
      return new $class($client, $response->json());
    }
  }

/**
 * Create object from API data
 *
 * @param M2X $client
 * @param stdClass $data
 */
  public function __construct($client, $data) {
    $this->client = $client;
    $this->setData($data);
  }

/**
 * Returns the data of the resource
 *
 * @return array
 */
  public function data() {
    return $this->data;
  }

/**
 * Set the resource data properties
 *
 * @param array $data
 */
  protected function setData($data) {
    $this->data = $data;
  }

/**
 * Update a resource
 *
 * @param array $data
 * @return Resource
 */
  public function update($data = array()) {
    foreach ($data as $key => $value) {
      $this->{$key} = $value;
    }

    $postData = array();
    foreach (static::$properties as $name) {
      $postData[$name] = $this->data[$name];
    }

    $this->client->put(static::$path . '/' . $this->id(), $postData);

    return $this;
  }

/**
 * Deletes a resource
 *
 * @return void
 */
  public function delete() {
    $this->client->delete(static::$path . '/' . $this->id());
  }

/**
 * Returns the path to the resource
 *
 * @return string
 */
  public function path() {
    return static::$path . '/' . $this->id();
  }

/**
 * Magic method for accessing resource data properties directly
 *
 * @param string $name
 * @return mixed
 */
  public function __get($name) {
    if (array_key_exists($name, $this->data)) {
      return $this->data[$name];
    }
  }

/**
 * Magic method for setting resource data properties
 *
 * @param string $name
 * @param mixed $value
 */
  public function __set($name, $value) {
    if (in_array($name, static::$properties)) {
      $this->data[$name] = $value;
    }
  }
}
