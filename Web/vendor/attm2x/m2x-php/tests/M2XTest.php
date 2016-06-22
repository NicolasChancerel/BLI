<?php

use Att\M2X\M2X;

class M2XTest extends BaseTestCase {
 
 /**
  * testInit method
  *
  * @return void
  */
  public function testBasics() {
    $m2x = new M2X('foo-bar');
    $this->assertEquals('foo-bar', $m2x->apiKey());

    $result = $m2x->endpoint();
    $this->assertEquals('https://api-m2x.att.com/v2', $result);

    $m2x = new M2X('foo', array('endpoint' => 'http://foo.bar'));
    $result = $m2x->endpoint();
    $this->assertEquals('http://foo.bar', $result);
   }

/**
 * testStatus method
 *
 * @return void
 */
   public function testStatusSuccess() {
    $m2x = new M2X('foo-bar');

    $m2x->request = $this->getMockBuilder('Att\M2X\HttpRequest')
               ->setMethods(array('request'))
               ->getMock();

    $m2x->request->method('request')
           ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/status'))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('status_success')));

    $response = $m2x->status();
    $expected = '{"api":"OK","triggers":"OK"}';
    $this->assertSame(200, $response->statusCode);
    $this->assertEquals($expected, $response->body);
  }

/**
 * testGet method
 *
 * @return void
 */
  public function testGet() {
    $m2x = new M2X('abc123');

    $m2x->request = $this->getMockBuilder('Att\M2X\HttpRequest')
                         ->setMethods(array('header'))
                         ->getMock();

    $m2x->request->expects($this->at(0))
                 ->method('header')
                 ->with($this->equalTo('X-M2X-KEY'), $this->equalTo('abc123'));

    $m2x->get('/status');
  }

/**
 * testLastResponse method
 *
 * @return void
 */
  public function testLastResponse() {
    $m2x = $this->generateMockM2X();

    $response = new Att\M2X\HttpResponse($this->_raw('distributions_index_success'));
    $m2x->request->expects($this->at(0))->method('request')->willReturn($response);

    $secondResponse = new Att\M2X\HttpResponse($this->_raw('devices_get_success'));
    $m2x->request->expects($this->at(1))->method('request')->willReturn($secondResponse);

    $m2x->distributions();
    $this->assertSame($response, $m2x->lastResponse());

    $m2x->device('2dd1c43521cef93109a3e8a75d4d5a88');
    $this->assertSame($secondResponse, $m2x->lastResponse());
  }

/**
 * testLastResponseWithException method
 *
 * @return void
 */
  public function testLastResponseWithException() {
    $m2x = $this->generateMockM2X();

    $response = new Att\M2X\HttpResponse($this->_raw('devices_get_not_found'));
    $m2x->request->expects($this->once())->method('request')->willReturn($response);
    $exception = null;
    try {
      $m2x->device('foo');
    } catch (Exception $ex) {
      $exception = $ex;
    }

    $this->assertEquals(404, $exception->getCode());
    $this->assertSame($response, $m2x->lastResponse());
  }
}
