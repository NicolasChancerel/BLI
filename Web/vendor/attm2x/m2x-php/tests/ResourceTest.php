<?php

use Att\M2X\M2X;
use Att\M2X\Resource;

/**
 * Test class for unit testing the abstract Resource class
 *
 */
class MockResource extends Resource {

/**
 * Path of the resource
 *
 * @var string
 */
  public static $path = '/foo';

/**
 * Resource properties
 *
 * @var array
 */
  protected static $properties = array(
    'name', 'description', 'foo', 'bar'
  );

/**
 * Used for testing the magic __GET and __SET methods
 *
 * @var string
 */
  public $testVar = 'foobar';


/**
 * The resource id for the REST URL
 *
 * @return string
 */
  public function id() {
    return $this->id;
  }

/**
 * Helper method to retrieve a protected instance variable
 *
 * @param string $name
 * @return mied
 */
  public function getProtected($name) {
    return $this->{$name};
  }
}

class ResourceTest extends BaseTestCase {

/**
 * testMagickMethods method
 *
 * @return void
 */
  public function testMagicMethods() {
    $m2x = $this->generateMockM2X();

    $data = array(
      'name' => 'Test Resource',
      'description' => 'Foo Description',
      'foo' => 'abc123',
      'bar' => 10005,
      'readonly' => 'foobar'
    );

    $resource = new MockResource($m2x, $data);

    $this->assertEquals('Test Resource', $resource->name);
    $this->assertEquals(10005, $resource->bar);

    $resource->name = 'Edited Name';
    $result = $resource->getProtected('data');
    $this->assertEquals('Edited Name', $result['name']);
    $this->assertEquals('Edited Name', $resource->name);

    $resource->testVar = 'changed';
    $this->assertEquals('changed', $resource->testVar);
    $result = $resource->getProtected('data');
    $this->assertArrayNotHasKey('testVar', $result);

    //Test read only property
    $this->assertEquals('foobar', $resource->readonly);
    $resource->readonly = 'modified';
    $this->assertEquals('foobar', $resource->readonly);
    $result = $resource->getProtected('data');
    $this->assertEquals('foobar', $result['readonly']);
  }

/**
 * testData method
 *
 * @return void
 */
  public function testData() {
    $m2x = $this->generateMockM2X();

    $data = array(
      'name' => 'Test Resource',
      'description' => 'Foo Description',
      'foo' => 'abc123',
      'bar' => 10005,
      'readonly' => 'foobar'
    );

    $resource = new MockResource($m2x, $data);
    $result = $resource->data();
    $this->assertEquals($data, $result);
  }

/**
 * testUpdate method
 *
 * @return void
 */
  public function testUpdate() {
    $client = $this->getMockBuilder('Att\M2X\M2X')
                   ->setConstructorArgs(array('foobar'))
                   ->setMethods(array('put'))
                   ->getMock();

    $expectedPost = array(
      'name' => 'Original Name',
      'description' => 'Updated Description',
      'foo' => 'abc123',
      'bar' => 10005
    );
    $client->expects($this->once())->method('put')
           ->with($this->equalTo('/foo/112233'), $this->equalTo($expectedPost));

    $data = array(
      'id' => '112233',
      'name' => 'Original Name',
      'description' => 'Original Description',
      'foo' => 'abc123',
      'bar' => 10005,
      'readonly' => 'Original Readonly'
    );

    $resource = new MockResource($client, $data);
    $update = array(
      'description' => 'Updated Description',
      'readonly' => 'Updated Readonly' //Check private property
    );
    $resource->update($update);
  }

/**
 * testPath method
 *
 * @return void
 */
  public function testPath() {
    $client = $this->getMockBuilder('Att\M2X\M2X');

    $data = array(
      'id' => '150034',
      'name' => 'Original Name',
      'description' => 'Original Description',
      'foo' => 'abc123',
      'bar' => 10005
    );

    $resource = new MockResource($client, $data);
    $this->assertEquals('/foo/150034', $resource->path());
  }
}
