<?php

use Att\M2X\M2X;
use Att\M2X\Device;
use Att\M2X\Trigger;

class TriggerTest extends BaseTestCase {

/**
 * testOriginalGetDisabled method
 *
 * @expectedException BadMethodCallException
 *
 * @return void
 */
  public function testOriginalGetDisabled() {
    $m2x = $this->generateMockM2X();
    Trigger::get($m2x, 'foo');
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
    Trigger::create($m2x, array());
  }


/**
 * testGet method
 *
 * @return void
 */
  public function testGet() {
    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/devices/271b4b18b86a3d4d0cdcb9f41ca0ad46/triggers/AUo16A-D8j1J6JfFlTV9'))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('devices_trigger_get_success')));

    $device = new Device($m2x, array('id' => '271b4b18b86a3d4d0cdcb9f41ca0ad46'));
    $result = $device->trigger('AUo16A-D8j1J6JfFlTV9');

    $this->assertEquals('Test Trigger', $result->name);
    $this->assertSame($device, $result->parent);
    $this->assertEquals('AUo16A-D8j1J6JfFlTV9', $result->id());

    $expected = '/devices/271b4b18b86a3d4d0cdcb9f41ca0ad46/triggers/AUo16A-D8j1J6JfFlTV9';
    $this->assertEquals($expected, $result->path());
  }

/**
 * testCreate method
 *
 * @return void
 */
  public function testCreate() {
    $data = array(
      'name' => 'Test Trigger',
      'stream' => 'test-stream',
      'condition' => '>',
      'value' => 50,
      'callback_url' => 'http://example.com',
      'status' => 'enabled'
    );

    $m2x = $this->generateMockM2X();

    $m2x->request->expects($this->once())->method('request')
           ->with($this->equalTo('POST'), $this->equalTo('https://api-m2x.att.com/v2/devices/271b4b18b86a3d4d0cdcb9f41ca0ad46/triggers'), $this->equalTo($data))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('devices_trigger_post_success')));

    $device = new Device($m2x, array('id' => '271b4b18b86a3d4d0cdcb9f41ca0ad46'));

    $result = $device->createTrigger($data);
    $this->assertInstanceOf('Att\M2X\Trigger', $result);
    $this->assertSame($device, $result->parent);
  }
}
