<?php

use Att\M2X\HttpResponse;

class HttpResponseTest extends BaseTestCase {

/**
 * testBasics method
 *
 * @return void
 */
  public function testSuccessResponse() {
    $response = new Att\M2X\HttpResponse($this->_raw('devices_update_location_success'));

    $expected = '{"status":"accepted"}';
    $this->assertEquals($expected, $response->raw());

    $expected = array('status' => 'accepted');
    $this->assertEquals($expected, $response->json());

    $expected = array(
      'Server' => 'nginx',
      'Date' => 'Tue, 09 Dec 2014 17:48:00 GMT',
      'Content-Type' => 'application/json',
      'Content-Length' => '21',
      'Status' => '202 Accepted',
      'X-M2X-VERSION' => 'v2.3.2-alpha',
      'Vary' => 'Accept-Encoding'
    );
    $this->assertEquals($expected, $response->headers());

    $this->assertEquals(202, $response->status());
    $this->assertTrue($response->success());
    $this->assertFalse($response->clientError());
    $this->assertFalse($response->serverError());
    $this->assertFalse($response->error());
  }

/**
 * testClientError method
 *
 * @return void
 */
  public function testClientError() {
    $response = new Att\M2X\HttpResponse($this->_raw('devices_get_not_found'));

    $this->assertEquals(404, $response->status());
    $this->assertFalse($response->success());
    $this->assertTrue($response->clientError());
    $this->assertFalse($response->serverError());
    $this->assertTrue($response->error());
  }

/**
 * testServerError method
 *
 * @return void
 */
  public function testServerError() {
    $response = new Att\M2X\HttpResponse($this->_raw('internal_server_error'));

    $this->assertEquals(500, $response->status());
    $this->assertFalse($response->success());
    $this->assertFalse($response->clientError());
    $this->assertTrue($response->serverError());
    $this->assertTrue($response->error());
  }
}
