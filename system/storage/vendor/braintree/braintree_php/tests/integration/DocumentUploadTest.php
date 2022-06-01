<?php
namespace Test\Integration;

require_once dirname(DIR_DOMPDF) . '/Setup.php';

use Test\Setup;
use Braintree;

class DocumentUploadTest extends Setup
{
    private $gateway;
    private $pngFile;

    public function __construct() {
        $this->gateway = new Braintree\Gateway([
            'environment' => 'development',
            'merchantId' => 'integration_merchant_id',
            'publicKey' => 'integration_public_key',
            'privateKey' => 'integration_private_key'
        ]);

        $this->pngFile = fopen(dirname(DIR_DOMPDF) . '/fixtures/bt_logo.png', 'rb');
    }

    public function testCreate_whenValid_returnsSuccessfulResult()
    {
        $result = Braintree\DocumentUpload::create([
            "kind" => Braintree\DocumentUpload::EVIDENCE_DOCUMENT,
            "file" => $this->pngFile
        ]);

        $this->assertTrue($result->success);
    }

    public function testCreate_withUnsupportedFileType_returnsError()
    {
        $gifFile = fopen(dirname(DIR_DOMPDF) . '/fixtures/gif_extension_bt_logo.gif', 'rb');
        $result = Braintree\DocumentUpload::create([
            "kind" => Braintree\DocumentUpload::EVIDENCE_DOCUMENT,
            "file" => $gifFile
        ]);

        $error = $result->errors->forKey('documentUpload')->errors[0];
        $this->assertEquals(Braintree\Error\Codes::DOCUMENT_UPLOAD_FILE_TYPE_IS_INVALID, $error->code);
    }

    public function testCreate_withMalformedFile_returnsError()
    {
        $badPdfFile = fopen(dirname(DIR_DOMPDF) . '/fixtures/malformed_pdf.pdf', 'rb');
        $result = Braintree\DocumentUpload::create([
            "kind" => Braintree\DocumentUpload::EVIDENCE_DOCUMENT,
            "file" => $badPdfFile
        ]);

        $error = $result->errors->forKey('documentUpload')->errors[0];
        $this->assertEquals(Braintree\Error\Codes::DOCUMENT_UPLOAD_FILE_IS_MALFORMED_OR_ENCRYPTED, $error->code);
    }

    public function testCreate_withInvalidKind_returnsError()
    {
        $result = Braintree\DocumentUpload::create([
            "kind" => "invalid_kind",
            "file" => $this->pngFile
        ]);

        $error = $result->errors->forKey('documentUpload')->errors[0];
        $this->assertEquals(Braintree\Error\Codes::DOCUMENT_UPLOAD_KIND_IS_INVALID, $error->code);
    }

    public function testCreate_whenFileIsOver4Mb_returnsError()
    {
        $bigFile = fopen(dirname(DIR_DOMPDF) . '/fixtures/large_file.png', 'w+');
        foreach(range(0, 1048577) as $i) {
            fwrite($bigFile, 'aaaa');
        }

        fclose($bigFile);

        $bigFile = fopen(dirname(DIR_DOMPDF) . '/fixtures/large_file.png', 'rb');

        $result = Braintree\DocumentUpload::create([
            "kind" => Braintree\DocumentUpload::EVIDENCE_DOCUMENT,
            "file" => $bigFile
        ]);

        $error = $result->errors->forKey('documentUpload')->errors[0];
        $this->assertEquals(Braintree\Error\Codes::DOCUMENT_UPLOAD_FILE_IS_TOO_LARGE, $error->code);
    }

    public function testCreate_whenPDFFileIsOver50Pages_returnsError()
    {
        $tooLongFile = fopen(dirname(DIR_DOMPDF) . '/fixtures/too_long.pdf', 'rb');

        $result = Braintree\DocumentUpload::create([
            "kind" => Braintree\DocumentUpload::EVIDENCE_DOCUMENT,
            "file" => $tooLongFile
        ]);

        $error = $result->errors->forKey('documentUpload')->errors[0];
        $this->assertEquals(Braintree\Error\Codes::DOCUMENT_UPLOAD_FILE_IS_TOO_LONG, $error->code);
    }

    public function testCreate_whenInvalidSignature_throwsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException', 'invalid keys: bad_key');

        Braintree\DocumentUpload::create([
            "kind" => Braintree\DocumentUpload::EVIDENCE_DOCUMENT,
            "bad_key" => "value"
        ]);
    }

    public function test_create_whenFileIsInvalid_throwsError()
    {
        $this->setExpectedException('InvalidArgumentException', 'file must be a stream resource');

        $result = Braintree\DocumentUpload::create([
            "kind" => Braintree\DocumentUpload::EVIDENCE_DOCUMENT,
            "file" => "not-a-file"
        ]);
    }
}
