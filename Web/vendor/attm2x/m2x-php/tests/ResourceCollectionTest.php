<?php

use Att\M2X\M2X;
use Att\M2X\Resource;
use Att\M2X\ResourceCollection;

/**
 * Test class for unit testing the abstract Resource class
 *
 */
class PostResource extends Resource {

/**
 * Path of the resource
 *
 * @var string
 */
  public static $path = '/posts';

/**
 * Resource properties
 *
 * @var array
 */
  protected static $properties = array(
    'title', 'description'
  );

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

class PostResourceCollection extends ResourceCollection {

  static protected $resourceClass = 'PostResource';

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

/**
 * Test class for unit testing non paginated resources
 *
 */
class CategoryResource extends Resource {

/**
 * Path of the resource
 *
 * @var string
 */
  public static $path = '/categories';

/**
 * Resource properties
 *
 * @var array
 */
  protected static $properties = array(
    'name'
  );

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

class CategoryResourceCollection extends ResourceCollection {

  static protected $resourceClass = 'CategoryResource';

  public $paginate = false;

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

class ResourceCollectionTest extends BaseTestCase {

/**
 * testConstructMethod
 *
 * @return void
 */
  public function testBasics() {
    $m2x = $this->generateMockM2X();

    $return = array(
      'total' => 3,
      'pages' => 1,
      'limit' => 10,
      'current_page' => 1,
      'posts' => array(
        array('id' => '1', 'title' => 'Foo', 'description' => 'abc'),
        array('id' => '2', 'title' => 'Bar', 'description' => 'def'),
        array('id' => '3', 'title' => 'M2X', 'description' => 'ghi')
      )
    );
    
    $m2x->request->expects($this->once())->method('request')
        ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/posts'), $this->equalTo(array('page' => 1)))
        ->willReturn(new Att\M2X\HttpResponse($this->_responseFromData($return)));

    $collection = new PostResourceCollection($m2x);
    $this->assertEquals(3, $collection->getProtected('total'));
    $this->assertEquals(1, $collection->getProtected('pages'));
    $this->assertEquals(1, $collection->getProtected('currentPage'));

    $this->assertEquals(3, count($collection));

    //Test iteration
    $result = $collection->current();
    $this->assertEquals('Foo', $result->title);

    $collection->next();
    $this->assertTrue($collection->valid());
    $result = $collection->current();
    $this->assertEquals('Bar', $result->title);

    $collection->next();
    $this->assertTrue($collection->valid());
    $result = $collection->current();
    $this->assertEquals('M2X', $result->title);
    $this->assertEquals(2, $collection->key());

    $collection->next();
    $this->assertFalse($collection->valid());

    $collection->rewind();
    $result = $collection->current();
    $this->assertEquals('Foo', $result->title);
    $this->assertEquals(0, $collection->key());
  }

/**
 * testMultiplePages method
 *
 * @return void
 */
  public function testMultiplePages() {
    $m2x = $this->generateMockM2X();

    $returns = array(
      array(
        'total' => 5,
        'pages' => 3,
        'limit' => 2,
        'current_page' => 1,
        'posts' => array(
          array('id' => '1', 'title' => 'First', 'description' => 'abc'),
          array('id' => '2', 'title' => 'Second', 'description' => 'def')
        )
      ),
      array(
        'total' => 5,
        'pages' => 3,
        'limit' => 2,
        'current_page' => 2,
        'posts' => array(
          array('id' => '3', 'title' => 'Third', 'description' => 'ghi'),
          array('id' => '4', 'title' => 'Fourth', 'description' => 'klm')
        )
      ),
      array(
        'total' => 5,
        'pages' => 3,
        'limit' => 2,
        'current_page' => 3,
        'posts' => array(
          array('id' => '5', 'title' => 'Fourth', 'description' => 'opq')
        )
      )
    );
    
    for ($i = 0; $i < 3; $i++) {
      $m2x->request->expects($this->at($i))->method('request')
          ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/posts'), $this->equalTo(array('page' => $i + 1)))
          ->willReturn(new Att\M2X\HttpResponse($this->_responseFromData($returns[$i])));
    }

    $collection = new PostResourceCollection($m2x);

    $this->assertEquals(5, count($collection));

    //Move pointer to the second page
    $this->assertEquals(1, $collection->page());
    $collection->next();
    $collection->next();
    $result = $collection->current();
    $this->assertEquals(2, $collection->page());
    $this->assertEquals('Third', $result->title);

    //Move pointer to the third page
    $collection->next();
    $collection->next();
    $this->assertTrue($collection->valid());

    $collection->next();
    $this->assertFalse($collection->valid());
  }

/**
 * testNonPaginated method
 *
 * @return void
 */
  public function testNonPaginated() {
    $m2x = $this->generateMockM2X();

    $return = array('categories' => array(
      array('id' => '1', 'name' => 'Foo'),
      array('id' => '2', 'name' => 'Bar'),
      array('id' => '3', 'name' => 'Hardware')
    ));

    $m2x->request->expects($this->once())->method('request')
        ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/categories'))
        ->willReturn(new Att\M2X\HttpResponse($this->_responseFromData($return)));

    $collection = new CategoryResourceCollection($m2x);
    $this->assertCount(3, $collection);

    foreach ($collection as $result) {
      $this->assertInstanceOf('CategoryResource', $result);
    }
  }

/**
 * Create a typical 200 OK response with JSON data
 *
 * @param array $data
 * @return string
 */
  protected function _responseFromData($data) {
    $resp = "HTTP/1.1 200 OK\r\n";
    $resp .= "Server: nginx\r\n";
    $resp .= "Date: Mon, 08 Dec 2014 21:01:02 GMT\r\n";
    $resp .= "\r\n";
    $resp .= json_encode($data);
    return $resp;
  }
}
