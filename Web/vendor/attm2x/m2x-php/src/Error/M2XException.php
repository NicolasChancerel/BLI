<?php

namespace Att\M2X\Error;

use Att\M2X\HttpResponse;

class M2XException extends \Exception {

/**
 * Holds the HttpResponse instance
 *
 * @var HttpResponse
 */
  public $response = null;

/**
 * Create the exception from a HttpResponse object
 *
 * @param HttpResponse $response
 */
  public function __construct(HttpResponse $response) {
    $data = $response->json();
    $this->response = $response;
    parent::__construct($data['message'], $response->statusCode);
  }
}
