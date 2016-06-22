<?php

namespace Att\M2X;

use Att\M2X\Error\M2XException;

class M2X {

  const VERSION = '3.0.1';
  const DEFAULT_API_BASE = 'https://api-m2x.att.com';
  const DEFAULT_API_VERSION = 'v2';

/**
 * The full URI to the M2X API
 *
 * @var string
 */
    protected $endpoint;

/**
 * Holds the API Key
 *
 * @var string
 */
  protected $apiKey;

/**
 * Holds the instance that does all HTTP requests
 *
 * @var HttpRequest
 */
  public $request;

/**
 * Holds the user agent string
 *
 * @var string
 */
  protected $userAgent = '';

/**
 * Holds a reference to the last received HttpResponse
 *
 * @var HttpResponse
 */
  protected $lastResponse = null;

/**
 * Creates a new instance of the M2X API.
 *
 * Options:
 * - endpoint: Configure a custom endpoint (optional)
 *
 * @param string $apiKey
 * @param array $options
 * @return void
 */
  public function __construct($apiKey, $options = array()) {
    $this->apiKey = $apiKey;

    if (isset($options['endpoint'])) {
      $this->endpoint = $options['endpoint'];
    } else {
      $this->endpoint = self::DEFAULT_API_BASE . '/' . self::DEFAULT_API_VERSION;
    }
    
    $this->userAgent = $this->userAgent();
  }

/**
 * Returns the API Key
 *
 * @return string
 */
  public function apiKey() {
    return $this->apiKey;
  }

/**
 * Returns the full URI to the M2X API.
 *
 * @return string
 */
  public function endpoint() {
    return $this->endpoint;
  }

/**
 * Returns the API status.
 *
 * @return HttpResponse
 */
  public function status() {
    return $this->get('/status');
  }

/**
 * Retrieve a list of keys associated with the user account.
 *
 * https://m2x.att.com/developer/documentation/v2/keys#List-Keys
 *
 * @return array
 */
  public function keys() {
    return Key::index($this);
  }

/**
 * Retrieve a single key from the API.
 *
 * This method instantiates an instance of Key with
 * all its attributes initialized.
 * 
 * @param string $key
 * @return Key
 */
  public function key($key) {
    return Key::get($this, $key);
  }

/**
 * Create a new account key.
 *
 * @link https://m2x.att.com/developer/documentation/v2/keys#Create-Key
 *
 * @param  $data
 * @return Key
 */
  public function createKey($data) {
    return Key::create($this, $data);
  }

/**
 * Retrieve a list of distributions associated with the user account.
 *
 * @param $params
 * @return DistributionCollection
 */
  public function distributions($params = array()) {
    return new DistributionCollection($this, $params);
  }

/**
 * Retrieve a single distribution from the API.
 *
 * This method instantiates an instance of Distribution
 * with all its attributes initialized.
 *
 * @param string $id
 * @return Key
 */
  public function distribution($id) {
    return Distribution::get($this, $id);
  }

/**
 * Create a new distribution.
 *
 * @param  $data
 * @return Device
 */
  public function createDistribution($data) {
    return Distribution::create($this, $data);
  }

/**
 * Retrieve a list of devices associated with the user account.
 *
 * @param $params
 * @return DeviceCollection
 */
  public function devices($params = array()) {
    return new DeviceCollection($this, $params);
  }

/**
 * Retrieve the list of devices accessible by the authenticated
 * API key that meet the search criteria.
 *
 * @param array $params
 * @return DeviceCollection
 */
  public function deviceCatalog($params = array()) {
    return new DeviceCollection($this, $params, true);
  }

/**
 * Retrieve a single device from the API.
 *
 * This method instantiates an instance of Device
 * with all its attributes initialized.
 *
 * @param string $key
 * @return Key
 */
  public function device($id) {
    return Device::get($this, $id);
  }

/**
 * Create a new device.
 *
 * @param  $data
 * @return Device
 */
  public function createDevice($data) {
    return Device::create($this, $data);
  }

/**
 * Returns the device tags
 *
 * @link https://m2x.att.com/developer/documentation/v2/device#List-Device-Tags
 *
 * @return array
 */
  public function deviceTags() {
    $response = $this->get('/devices/tags');
    return $response->json();
  }

/**
 * Perform a GET request to the API.
 *
 * @param string $path
 * @param array $params
 * @return HttpResponse
 * @throws M2XException
 */
  public function get($path, $params = array()) {
    $request = $this->request();
    $request = $this->prepareRequest($request);

    $response = $request->get($this->endpoint . $path, $params);
    return $this->handleResponse($response);
  }

/**
 * Perform a POST request to the API.
 *
 * @param string $path
 * @param array $vars
 * @return HttpResponse
 * @throws M2XException
 */
  public function post($path, $vars = array()) {
    $request = $this->request();
    $request = $this->prepareRequest($request);

    $response = $request->post($this->endpoint . $path, $vars);
    return $this->handleResponse($response);
  }

/**
 * Perform a PUT request to the API.
 *
 * @param string $path
 * @param array $vars
 * @return HttpResponse
 * @throws M2XException
 */
  public function put($path, $vars = array()) {
    $request = $this->request();
    $request = $this->prepareRequest($request);

    $response = $request->put($this->endpoint . $path, $vars);
    return $this->handleResponse($response);
  }

/**
 * Perform a DELETE request to the API.
 *
 * @param string $path
 * @return HttpResponse
 * @throws M2XException
 */
  public function delete($path) {
    $request = $this->request();
    $request = $this->prepareRequest($request);

    $response = $request->delete($this->endpoint . $path);
    return $this->handleResponse($response);
  }

/**
 * Sets the common headers for each request to the API.
 *
 * @param HttpRequest $request
 * @return HttpRequest
 */
  protected function prepareRequest($request) {
    $request->header('X-M2X-KEY', $this->apiKey);
    $request->header('User-Agent', $this->userAgent);
    return $request;
  }

/**
 * Checks the HttpResponse for errors and throws an exception, if
 * no errors are encountered, the HttpResponse is returned.
 *
 * @param HttpResponse $response
 * @return HttpResponse
 * @throws M2XException
 */
  protected function handleResponse(HttpResponse $response) {
    $this->lastResponse = $response;

    if ($response->success()) {
      return $response;
    }

    throw new M2XException($response);
  }

/**
 * Generate the user agent string
 *
 * @return string
 */
  public function userAgent() {
    $version = self::VERSION;
    $phpVersion = phpversion();
    $os = php_uname();

    return "M2X-PHP/{$version} PHP/{$phpVersion} ({$os})";
  }

/**
 * Creates an instance of the HttpRequest if it doesnt exist yet.
 *
 * @return HttpRequest
 */
  private function request() {
    if (!$this->request) {
      $this->request = new HttpRequest();
    }

    return $this->request;
  }

/**
 * The last received response
 *
 * @return HttpResponse
 */
  public function lastResponse() {
    return $this->lastResponse;
  }
}
