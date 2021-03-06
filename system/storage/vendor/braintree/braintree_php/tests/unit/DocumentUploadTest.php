<?php
namespace Test\Unit;

require_once dirname(DIR_DOMPDF) . '/Setup.php';

use Test\Setup;
use Braintree;

class DocumentUploadTest extends Setup
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateThrowsExceptionWithBadKeys()
    {
        $this->setExpectedException('InvalidArgumentException', 'invalid keys: bad_key');

        Braintree\DocumentUpload::create(["bad_key" => "value"]);
    }
}
