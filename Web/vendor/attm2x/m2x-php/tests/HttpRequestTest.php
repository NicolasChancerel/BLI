<?php

use Att\M2X\HttpRequest;

class HttpRequestTest extends BaseTestCase {

/**
 * testGet method
 *
 * @return void
 */
  public function testGet() {
    $request = new HttpRequest();

    $response = $request->get('httpbin.org/get', array('foo' => 'bar'));
    $this->assertEquals(200, $response->statusCode);

    $result = $response->json();
    $this->assertEquals(array('foo' => 'bar'), $result['args']);

    //Custom header
    $result = $request->header('X-Custom-Header', 'foobar');
    $this->assertSame($request, $result);
    $response = $request->get('httpbin.org/get');
    $this->assertEquals(200, $response->statusCode);

    $result = $response->json();
    $this->assertArrayHasKey('X-Custom-Header', $result['headers']);
    $this->assertEquals('foobar', $result['headers']['X-Custom-Header']);
    $this->assertEquals(array(), $result['args']);
  }

/**
 * testPost method
 *
 * @return void
 */
  public function testPost() {
    $request = new HttpRequest();

    $response = $request->post('httpbin.org/post', array('foo' => '123'));
    $this->assertEquals(200, $response->statusCode);
    $result = $response->json();

    $this->assertEquals('{"foo":"123"}', $result['data']);
    $this->assertEquals('application/json', $result['headers']['Content-Type']);

    //Test POST with no data
    $response = $request->post('httpbin.org/post');
    $this->assertEquals(200, $response->statusCode);
    $result = $response->json();

    $this->assertEquals('', $result['data']);
    $this->assertEquals('application/json', $result['headers']['Content-Type']);
    $this->assertEquals('0', $result['headers']['Content-Length']);
  }

/**
 * testPut method
 *
 * @return void
 */
  public function testPut() {
    $request = new HttpRequest();

    $response = $request->put('httpbin.org/put', array('foo' => 'bar'));
    $this->assertEquals(200, $response->statusCode);
    $result = $response->json();

    $this->assertEquals('{"foo":"bar"}', $result['data']);
    $this->assertEquals('application/json', $result['headers']['Content-Type']);

    //Test POST with no data
    $response = $request->put('httpbin.org/put');
    $this->assertEquals(200, $response->statusCode);
    $result = $response->json();

    $this->assertEquals('', $result['data']);
    $this->assertEquals('application/json', $result['headers']['Content-Type']);
    $this->assertEquals('0', $result['headers']['Content-Length']);
  }

/**
 * testDelete method
 *
 * @return void
 */
  public function testDelete() {
    $request = new HttpRequest();

    $response = $request->delete('httpbin.org/delete');
    $this->assertEquals(200, $response->statusCode);
    $result = $response->json();
    $this->assertEquals(array(), $result['args']);
  }
}
