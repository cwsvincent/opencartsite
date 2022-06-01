<?php
namespace Test\Unit;

require_once dirname(DIR_DOMPDF) . '/Setup.php';

use Test\Setup;
use Braintree;

class SubscriptionTest extends Setup
{
    public function testErrorsOnFindWithBlankArgument()
    {
        $this->setExpectedException('InvalidArgumentException');
        Braintree\Subscription::find('');
    }

    public function testErrorsOnFindWithWhitespaceArgument()
    {
        $this->setExpectedException('InvalidArgumentException');
        Braintree\Subscription::find('\t');
    }
}
