<?php

use Att\M2X\M2X;
use Att\M2X\Distribution;
use Att\M2X\DistributionCollection;

class DistributionCollectionTest extends BaseTestCase {

/**
 * testIndex method
 *
 * @return void
 */
  public function testSinglePage() {
    $m2x = $this->generateMockM2X();

    $m2x->request->method('request')
           ->with($this->equalTo('GET'), $this->equalTo('https://api-m2x.att.com/v2/distributions'))
           ->willReturn(new Att\M2X\HttpResponse($this->_raw('distributions_index_success')));

    $collection = $m2x->distributions();
    
    foreach ($collection as $resource) {
      $this->assertInstanceOf('Att\M2X\Distribution', $resource);
    }
  }
}
