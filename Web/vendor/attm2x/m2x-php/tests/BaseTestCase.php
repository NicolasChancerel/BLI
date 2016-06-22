<?php

use Att\M2X\M2X;

abstract class BaseTestCase extends PHPUnit_Framework_TestCase {
  
/**
 * Returns a raw mock response
 *
 * @param string $name
 * @return string
 */
  protected function _raw($name) {
    return file_get_contents(__DIR__ . '/responses/' . $name . '.txt');
  }

/**
 * Returns a mocked instance of the M2X client class
 *
 * @param string $key Optional api key
 * @return M2X
 */
  protected function generateMockM2X($apiKey = 'foo-bar') {
    $m2x = new M2X($apiKey);

    $m2x->request = $this->getMockBuilder('Att\M2X\HttpRequest')
                         ->setMethods(array('request'))
                         ->getMock();

    return $m2x;
  }
}
