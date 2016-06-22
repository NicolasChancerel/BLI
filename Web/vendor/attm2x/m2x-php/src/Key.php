<?php

namespace Att\M2X;

class Key extends Resource {

/**
 * REST path of the resource
 *
 * @var string
 */
  public static $path = '/keys';

/**
 * The Key resource properties
 *
 * @var array
 */
  protected static $properties = array(
    'name', 'stream', 'expires_at', 'origin',
    'permissions', 'device_access'
  );

/**
 * The resource id for the REST URL
 *
 * @return string
 */
  public function id() {
    return $this->key;
  }

/**
 * Regenerate an existing key
 *
 * @link https://m2x.att.com/developer/documentation/v2/keys#Regenerate-Key
 *
 * @return Key
 */
  public function regenerate() {
    $response = $this->client->post(self::$path . '/' . $this->key . '/regenerate');
    $this->setData($response->json());
    return $this;
  }
}
