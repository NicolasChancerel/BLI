<?php

use Att\M2X\M2X;
use Att\M2X\Device;
use Att\M2X\DeviceCollection;

class DeviceCollectionTest extends BaseTestCase {

/**
 * testIndex method
 *
 * @return void
 */
  public function testSinglePage() {
    $m2x = $this->generateMockM2X();

    $m2x->request->method('request')
           ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/devices'))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('devices_index_success')));

    $collection = new DeviceCollection($m2x);
    $this->assertCount(3, $collection);

    foreach ($collection as $resource) {
      $this->assertInstanceOf('Att\M2X\Device', $resource);
    }
  }

/**
 * testParameters method
 *
 * @return void
 */
  public function testParameters() {
    $m2x = $this->generateMockM2X();

    $m2x->request->method('request')
           ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/devices'), $this->equalTo(array('q' => 'Foo', 'page' => 1)))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('devices_index_success')));
    $params = array('q' => 'Foo');
    $collection = new DeviceCollection($m2x, $params);
  }

/**
 * testCatalog method
 *
 * @return void
 */
  public function testCatalog() {
    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/devices/catalog'), $this->equalTo(array('q' => 'CPU', 'page' => 1)))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('devices_catalog_success')));

    $collection = $m2x->deviceCatalog(array('q' => 'CPU'));
    $this->assertCount(2, $collection);
  }
}
