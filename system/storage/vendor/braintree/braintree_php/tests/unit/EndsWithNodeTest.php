<?php
namespace Test\Unit;

require_once dirname(DIR_DOMPDF) . '/Setup.php';

use Test\Setup;
use Braintree;

class EndsWithNodeTest extends Setup
{
  public function testEndsWith()
  {
      $node = new Braintree\EndsWithNode('field');
      $node->endsWith('value');
      $this->assertEquals(['ends_with' => 'value'], $node->toParam());
  }
}
