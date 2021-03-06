<?php
namespace Test\Unit\Result;

require_once dirname(dirname(DIR_DOMPDF)) . '/Setup.php';

use Test\Setup;
use Braintree;

class ErrorTest extends Setup
{
    public function testCallingNonExsitingFieldReturnsNull()
    {
        $result = new Braintree\Result\Error(['errors' => [], 'params' => [], 'message' => 'briefly describe']);
        $this->assertNull($result->transaction);
    }
}
