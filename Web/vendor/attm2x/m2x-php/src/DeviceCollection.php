<?php

namespace Att\M2X;

use Att\M2X\Device;

class DeviceCollection extends ResourceCollection {

/**
 * The resource class used in the collection
 *
 * @var string
 */
  protected static $resourceClass = 'Att\M2X\Device';

/**
 * Holds the flag that decided if this collection is
 * refering to the public devices or not.
 *
 * @var boolean
 */
  protected $catalog = false;

/**
 * The parent resource that this collection belongs to
 *
 * @var Resource
 */
  public $parent = null;

/**
 * Device collection constructor
 *
 * @param M2X $client
 * @param array $params
 * @param boolean $catalog Search in the catalog
 */
  public function __construct(M2X $client, $params = array(), $catalog = false, $parent = null) {
    $this->catalog = $catalog;

    if ($parent) {
      $this->parent = $parent;
      $this->catalog = false;
    }

    parent::__construct($client, $params);
  }

/**
 * Return the API path for the query
 *
 * @return void
 */
  protected function path() {
    if ($this->parent) {
      return $this->parent->path() . '/devices';
    }

    $class = static::$resourceClass;
    $path = $class::$path;
    if ($this->catalog) {
      $path .= '/catalog';
    }
    return $path;
  }
}
