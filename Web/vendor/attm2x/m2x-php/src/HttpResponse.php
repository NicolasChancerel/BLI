<?php

namespace Att\M2X;

class HttpResponse {

/**
 * The HTTP status code
 *
 * @var integer
 */
  public $statusCode;

/**
 * The response headers
 *
 * @var array
 */
  public $headers = array();

/**
 * The response body
 *
 * @var string
 */
  public $body = '';

/**
 * The full raw response
 *
 * @var string
 */
  public $raw = '';

/**
 * Parse the CURL response data
 *
 * @param string $response
 */
  public function __construct($response) {
    $this->raw = $response;
    $pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';

    preg_match_all($pattern, $response, $matches);
    $headers_string = array_pop($matches[0]);
    $headers = explode("\r\n", str_replace("\r\n\r\n", '', $headers_string));

    $status = array_shift($headers);
    preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $status, $matches);
    $this->statusCode = (int) $matches[2];

    foreach ($headers as $header) {
      preg_match('#(.*?)\:\s(.*)#', $header, $matches);
      $this->headers[$matches[1]] = $matches[2];
    }

    $this->body = str_replace($headers_string, '', $response);
  }

/**
 * Returns the raw response body
 *
 * @return string
 */
  public function raw() {
    return $this->body;
  }

/**
 * Returns the json encoded data object
 *
 * @return array
 */
  public function json() {
    return json_decode($this->body, true);
  }

/**
 * Returns the HTTP Status code
 *
 * @return int
 */
  public function status() {
    return $this->statusCode;
  }

/**
 * Whether response status is a success (status code 2xx)
 *
 * @return boolean
 */
  public function success() {
    return $this->statusCode >= 200 && $this->statusCode < 300;
  }

/**
 * Whether response status is a client error (status code 4xx)
 *
 * @return boolean
 */
  public function clientError() {
    return $this->statusCode >= 400 && $this->statusCode < 500;
  }

/**
 * Whether response status is a server error (status code 5xx)
 *
 * @return boolean
 */
  public function serverError() {
    return $this->statusCode >= 500 && $this->statusCode < 600;
  }

/**
 * Wheter response status is a client or server error
 *
 * @return boolean
 */
  public function error() {
    return $this->clientError() || $this->serverError();
  }

/**
 * Returns the headers included on the response
 *
 * @return array
 */
  public function headers() {
    return $this->headers;
  }
}
