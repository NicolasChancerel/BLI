<?php

namespace Att\M2X;

use Att\M2X\Device;
use Att\M2X\Trigger;

class TriggerCollection extends ResourceCollection {

/**
 * The resource class used in the collection
 *
 * @var string
 */
  protected static $resourceClass = 'Att\M2X\Trigger';

/**
 * Boolean flag to define if the resource collection
 * is paginated or not.
 *
 * @var boolean
 */
  protected $paginate = false;

/**
 * The parent resource that this collection belongs to
 *
 * @var Resource
 */
  public $parent = null;

/**
 * Resource collection constructor
 *
 * @param M2X $client
 */
  public function __construct(M2X $client, Resource $parent, $params = array()) {
    $this->parent = $parent;

    parent::__construct($client, $params);
  }

/**
 * Return the API path for the query
 *
 * @return void
 */
  protected function path() {
    $class = static::$resourceClass;
    return str_replace(':parent_path', $this->parent->path(), $class::$path);
  }

/**
 * Initialize and add a resource to the collection
 *
 * @param integer $i
 * @param array $data
 */
  protected function setResource($i, $data) {
    $this->resources[$i] = new static::$resourceClass($this->client, $this->parent, $data);
  }
}
