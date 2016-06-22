<?php

use Att\M2X\M2X;
use Att\M2X\Device;
use Att\M2X\Stream;

class StreamTest extends BaseTestCase {

/**
 * testOriginalGetDisabled method
 *
 * @expectedException BadMethodCallException
 *
 * @return void
 */
  public function testOriginalGetDisabled() {
    $m2x = $this->generateMockM2X();
    Stream::get($m2x, 'foo');
  }

/**
 * testOriginalCreateDisabled method
 *
 * @expectedException BadMethodCallException
 *
 * @return void
 */
  public function testOriginalCreateDisabled() {
    $m2x = $this->generateMockM2X();
    Stream::create($m2x, array());
  }

/**
 * testGet method
 *
 * @return void
 */
  public function testGet() {
    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/devices/c2b83dcb796230906c70854a57b66b0a/streams/stream_one'))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('streams_get_success')));

    $device = new Device($m2x, array('id' => 'c2b83dcb796230906c70854a57b66b0a'));
    $result = $device->stream('stream_one');

    $this->assertEquals('stream_one', $result->name);
    $this->assertSame($device, $result->parent);
    $this->assertEquals('stream_one', $result->id());
  }

/**
 * testCreate method
 *
 * @return void
 */
  public function testCreate() {
    $data = array('unit' => array('label' => 'Foo', 'symbol' => 'F'));

    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('PUT'), $this->equalTo('https://api-m2x.att.com/v2/devices/c2b83dcb796230906c70854a57b66b0a/streams/stream_foo'), $this->equalTo($data))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('streams_post_success')));

    $device = new Device($m2x, array('id' => 'c2b83dcb796230906c70854a57b66b0a'));

    $result = $device->updateStream('stream_foo', $data);
    $this->assertInstanceOf('Att\M2X\Stream', $result);
    $this->assertSame($device, $result->parent);
  }

/**
 * testUpdate method
 *
 * @return vod
 */
  public function testUpdate() {
    $data = array('unit' => array('label' => 'Foo', 'symbol' => 'F'));

    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->at(0))->method('request')
           ->with($this->equalTo('PUT'), $this->equalTo('https://api-m2x.att.com/v2/devices/c2b83dcb796230906c70854a57b66b0a/streams/stream_foo'), $this->equalTo($data))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('streams_update_success')));

    $m2x->request->expects($this->at(1))->method('request')
           ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/devices/c2b83dcb796230906c70854a57b66b0a/streams/stream_foo'))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('streams_update_get')));

    $device = new Device($m2x, array('id' => 'c2b83dcb796230906c70854a57b66b0a'));

    $result = $device->updateStream('stream_foo', $data);
    $this->assertInstanceOf('Att\M2X\Stream', $result);
    $this->assertSame($device, $result->parent);
  }

/**
 * testUpdateValue method
 *
 * @return void
 */
  public function testUpdateValue() {
    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->at(0))->method('request')
           ->with($this->equalTo('PUT'), $this->equalTo('https://api-m2x.att.com/v2/devices/c2b83dcb796230906c70854a57b66b0a/streams/stream_foo/value'), $this->equalTo(array('value' => 1234)))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('streams_update_value_success')));

    $m2x->request->expects($this->at(1))->method('request')
           ->with($this->equalTo('PUT'), $this->equalTo('https://api-m2x.att.com/v2/devices/c2b83dcb796230906c70854a57b66b0a/streams/stream_foo/value'), $this->equalTo(array('value' => 1123, 'timestamp' => '2014-10-01T12:00:00Z')))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('streams_update_value_success')));

    $device = new Device($m2x, array('id' => 'c2b83dcb796230906c70854a57b66b0a'));
    $stream = new Stream($m2x, $device, array('name' => 'stream_foo'));
    $stream->updateValue(1234);
    $stream->updateValue(1123, '2014-10-01T12:00:00Z');
  }

/**
 * testValues method
 *
 * @return void
 */
  public function testValues() {
    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/devices/c2b83dcb796230906c70854a57b66b0a/streams/stream_foo/values')) 
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('streams_values_get_success')));

    $device = new Device($m2x, array('id' => 'c2b83dcb796230906c70854a57b66b0a'));
    $stream = new Stream($m2x, $device, array('name' => 'stream_foo'));
    $result = $stream->values();
    $expected = array('limit', 'end', 'values');
    $this->assertEquals($expected, array_keys($result));
  }

/**
 * testStats method
 *
 * @return void
 */
  public function testStats() {
    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/devices/c2b83dcb796230906c70854a57b66b0a/streams/stream_foo/stats')) 
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('streams_stats_success')));

    $device = new Device($m2x, array('id' => 'c2b83dcb796230906c70854a57b66b0a'));
    $stream = new Stream($m2x, $device, array('name' => 'stream_foo'));
    $result = $stream->stats();
    $expected = array('end', 'stats');
    $this->assertEquals($expected, array_keys($result));
  }

/**
 * testPostValues method
 *
 * @return void
 */
  public function testPostValues() {
    $data = array(
      array('timestamp' => '2013-12-10T07:48:20+00:00', 'value' => 5002),
      array('timestamp' => '2013-12-12T07:48:20+00:00', 'value' => 5059)
    );
    $expectedParam = array('values' => $data);
    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('POST'), $this->equalTo('https://api-m2x.att.com/v2/devices/c2b83dcb796230906c70854a57b66b0a/streams/stream_foo/values')) 
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('streams_post_values_success')));

    $device = new Device($m2x, array('id' => 'c2b83dcb796230906c70854a57b66b0a'));
    $stream = new Stream($m2x, $device, array('name' => 'stream_foo'));

    $stream->postValues($data);
  }
}
