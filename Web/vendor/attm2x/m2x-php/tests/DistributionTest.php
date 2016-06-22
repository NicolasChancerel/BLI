<?php

use Att\M2X\M2X;
use Att\M2X\Distribution;

class DistributionTest extends BaseTestCase {

/**
 * testGet method
 *
 * @return void
 */
  public function testGet() {
    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/distributions/ce21d58783bd50c4e4dc04919d01e81b'))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('distributions_get_success')));

    $distribution = $m2x->distribution('ce21d58783bd50c4e4dc04919d01e81b');
    $this->assertInstanceOf('\Att\M2X\Distribution', $distribution);

    $this->assertEquals('Bar Distribution', $distribution->name);
  }

/**
 * testCreate method
 *
 * @return void
 */
  public function testCreate() {
    $data = array(
      'name' => 'FooBar Distribution',
      'visibility' => 'private'
    );

    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('POST'), $this->equalTo('https://api-m2x.att.com/v2/distributions'), $this->equalTo($data))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('distributions_post_success')));

    $result = $m2x->createDistribution($data);
    $this->assertInstanceOf('\Att\M2X\Distribution', $result);
  }

/**
 * testUpdate method
 *
 * @return void
 */
  public function testUpdate() {
    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('PUT'), $this->equalTo('https://api-m2x.att.com/v2/distributions/d447a2c499bc009d96a7d693a2e5b909'), $this->equalTo(array('name' => 'Updated', 'description' => '', 'visibility' => 'private')))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('distributions_put_success')));

    $data = array(
      'id' => 'd447a2c499bc009d96a7d693a2e5b909',
      'name' => 'FooBar Distribution',
      'description' => '',
      'visibility' => 'private',
      'serial' => '',
      'status' => 'enabled',
      'url' => 'http://api-m2x.att.com/v2/distributions/d447a2c499bc009d96a7d693a2e5b909',
      'created' => '2014-12-10T17:54:25.020Z',
      'updated' => '2014-12-10T17:54:25.020Z',
      'devices' => array(
        'total' => 0,
        'registered' => 0,
        'unregistered' => 0,
      ),
      'tags' => array()
    );
    $distribution = new Distribution($m2x, $data);

    $data = array(
      'name' => 'Updated',
      'serial' => 'Foo'
    );
    $result = $distribution->update($data);
    $this->assertSame($distribution, $result);
    $this->assertEquals('Updated', $result->name);
  }

/**
 * testDelete method
 *
 * @return void
 */
  public function testDelete() {
    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('DELETE'), $this->equalTo('https://api-m2x.att.com/v2/distributions/foobar'))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('distributions_delete_success')));

    $data = array(
      'id' => 'foobar',
      'name' => 'FooBar Distribution'
    );
    $distribution = new Distribution($m2x, $data);
    $distribution->delete();
  }

/**
 * testDevices method
 *
 * @return void
 */
  public function testDevices() {
    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/distributions/ce21d58783bd50c4e4dc04919d01e81b/devices'))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('distributions_devices_success')));

    $distribution = new Distribution($m2x, array('id' => 'ce21d58783bd50c4e4dc04919d01e81b'));

    $result = $distribution->devices();
    $this->assertInstanceOf('\Att\M2X\DeviceCollection', $result);
    $this->assertCount(2, $result);
    $this->assertSame($distribution, $result->parent);
  }

/**
 * testAddDevice method
 *
 * @return void
 */
  public function testAddDevice() {
    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('POST'), $this->equalTo('https://api-m2x.att.com/v2/distributions/ce21d58783bd50c4e4dc04919d01e81b/devices'), $this->equalTo(array('serial' => 'foobar')))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('distributions_add_device_success')));

    $distribution = new Distribution($m2x, array('id' => 'ce21d58783bd50c4e4dc04919d01e81b'));

    $result = $distribution->addDevice('foobar');
    $this->assertInstanceOf('\Att\M2X\Device', $result);
    $this->assertEquals('foobar', $result->serial);

  }
}
