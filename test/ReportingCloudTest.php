<?php

namespace TXTextControlTest\ReportingCloud;

use PHPUnit_Framework_TestCase;

use TXTextControl\ReportingCloud\Exception\RuntimeException;
use TXTextControl\ReportingCloud\Exception\InvalidArgumentException;
use TXTextControl\ReportingCloud\ReportingCloud;

class ReportingCloudTest extends PHPUnit_Framework_TestCase
{
    protected $reportingCloud;

    public function setUp()
    {
        $this->reportingCloud = new ReportingCloud();

        $this->reportingCloud->setUsername(reporting_cloud_username());
        $this->reportingCloud->setPassword(reporting_cloud_password());
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function testGetTemplateThumbnails()
    {
        $response = $this->reportingCloud->getTemplateThumbnails('sample_invoice.tx', 100, 1, 1, 'PNG');

        $this->assertArrayHasKey(0, $response);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetTemplateThumbnails_InvalidTemplateName()
    {
        $this->reportingCloud->getTemplateThumbnails('sample_invoice.xx', 100, 1, 1, 'PNG');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetTemplateThumbnails_InvalidZoomFactor()
    {
        $this->reportingCloud->getTemplateThumbnails('sample_invoice.tx', -1, 1, 1, 'PNG');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetTemplateThumbnails_InvalidFromPage()
    {
        $this->reportingCloud->getTemplateThumbnails('sample_invoice.tx', 100, -1, 1, 'PNG');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetTemplateThumbnails_InvalidToPage()
    {
        $this->reportingCloud->getTemplateThumbnails('sample_invoice.tx', 100, 1, -1, 'PNG');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetTemplateThumbnails_InvalidImageFormat()
    {
        $this->reportingCloud->getTemplateThumbnails('sample_invoice.tx', 100, 1, 1, 'XXX');
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function testGetTemplateCount()
    {
        $response = $this->reportingCloud->getTemplateCount();

        $this->assertTrue(is_integer($response));

        $this->assertGreaterThan(0, $response);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTemplateExists_InvalidTemplateName()
    {
        $this->reportingCloud->templateExists('sample_invoice.xx');
    }

    public function testTemplateExists()
    {
        $testTemplateFilename = $this->getTestTemplateFilename();
        $tempTemplateFilename = $this->getTempTemplateFilename();
        $tempTemplateName     = basename($tempTemplateFilename);

        copy($testTemplateFilename, $tempTemplateFilename);

        $response = $this->reportingCloud->uploadTemplate($tempTemplateFilename);

        $this->assertTrue($response);

        $response = $this->reportingCloud->templateExists($tempTemplateName);

        $this->assertTrue($response);

        $response = $this->reportingCloud->deleteTemplate($tempTemplateName);

        $this->assertTrue($response);

        $response = $this->reportingCloud->templateExists($tempTemplateName);

        $this->assertFalse($response);

        unlink($tempTemplateFilename);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetTemplatePageCount_InvalidTemplateName()
    {
        $this->reportingCloud->getTemplatePageCount('sample_invoice.xx');
    }

    public function testGetTemplatePageCount()
    {
        $testTemplateFilename = $this->getTestTemplateFilename();
        $tempTemplateFilename = $this->getTempTemplateFilename();
        $tempTemplateName     = basename($tempTemplateFilename);

        copy($testTemplateFilename, $tempTemplateFilename);

        $response = $this->reportingCloud->uploadTemplate($tempTemplateFilename);

        $this->assertTrue($response);

        $response = $this->reportingCloud->getTemplatePageCount($tempTemplateName);

        $this->assertTrue(is_integer($response));
        $this->assertEquals(1, $response);

        $response = $this->reportingCloud->deleteTemplate($tempTemplateName);

        $this->assertTrue($response);

        unlink($tempTemplateFilename);
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function testGetTemplateList()
    {
        $response = $this->reportingCloud->getTemplateList();

        $this->assertTrue(is_array($response));

        $this->assertArrayHasKey(0, $response);

        $this->assertArrayHasKey('template_name', $response[0]);
        $this->assertArrayHasKey('modified'     , $response[0]);
        $this->assertArrayHasKey('size'         , $response[0]);
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function testGetAccountSettings()
    {
        $response = $this->reportingCloud->getAccountSettings();

        $this->assertTrue(is_array($response));

        $this->assertArrayHasKey('serial_number'     , $response);
        $this->assertArrayHasKey('created_documents' , $response);
        $this->assertArrayHasKey('uploaded_templates', $response);
        $this->assertArrayHasKey('max_documents'     , $response);
        $this->assertArrayHasKey('max_templates'     , $response);
        $this->assertArrayHasKey('valid_until'       , $response);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConvertDocument_InvalidDocumentFilename()
    {
        $this->reportingCloud->convertDocument('/invalid/path/document.doc', 'PDF');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConvertDocument_InvalidReturnFormat()
    {
        $documentFilename = $this->getTestTemplateFilename();

        $this->reportingCloud->convertDocument($documentFilename, 'XXX');
    }

    public function testConvertDocument()
    {
        $documentFilename = $this->getTestDocumentFilename();

        $response = $this->reportingCloud->convertDocument($documentFilename, 'PDF');
        $responseLength = mb_strlen($response);

        $this->assertNotNull($response);
        $this->assertNotFalse($response);
        $this->assertTrue($responseLength > 1024);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMerge_InvalidReturnFormat()
    {
        $mergeData = $this->getTestTemplateMergeData();

        $this->reportingCloud->mergeDocument($mergeData, 'X');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMerge_InvalidTemplateName()
    {
        $mergeData = $this->getTestTemplateMergeData();

        $this->reportingCloud->mergeDocument($mergeData, 'PDF', '../invalid_template.tx');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMerge_InvalidTemplateFilename()
    {
        $mergeData = $this->getTestTemplateMergeData();

        $this->reportingCloud->mergeDocument($mergeData, 'PDF', null, '/invalid/path/template.doc');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMerge_InvalidAppend()
    {
        $mergeData        = $this->getTestTemplateMergeData();
        $templateFilename = $this->getTestTemplateFilename();

        $this->reportingCloud->mergeDocument($mergeData, 'PDF', null, $templateFilename, 1);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMerge_InvalidMergeSettings()
    {
        $mergeData        = $this->getTestTemplateMergeData();
        $templateFilename = $this->getTestTemplateFilename();

        $this->reportingCloud->mergeDocument($mergeData, 'PDF', null, $templateFilename, true, 1);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMerge_InvalidMergeSettingsBooleanValues()
    {
        $mergeData            = $this->getTestTemplateMergeData();
        $mergeSettings        = $this->getTestTemplateMergeSettings();

        $testTemplateFilename = $this->getTestTemplateFilename();

        $mergeSettings['remove_empty_blocks'] = 'invalid';  // value must be boolean
        $mergeSettings['remove_empty_fields'] = 'invalid';
        $mergeSettings['remove_empty_images'] = 'invalid';

        $this->reportingCloud->mergeDocument($mergeData, 'PDF', null, $testTemplateFilename, false, $mergeSettings);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMerge_InvalidMergeSettingsTimestampValues()
    {
        $mergeData            = $this->getTestTemplateMergeData();
        $mergeSettings        = $this->getTestTemplateMergeSettings();

        $testTemplateFilename = $this->getTestTemplateFilename();

        $mergeSettings['creation_date']          = -1;  // value must be timestamp
        $mergeSettings['last_modification_date'] = 'invalid';

        $this->reportingCloud->mergeDocument($mergeData, 'PDF', null, $testTemplateFilename, false, $mergeSettings);
    }

    public function testMergeWithTemplateName()
    {
        $mergeData            = $this->getTestTemplateMergeData();
        $mergeSettings        = $this->getTestTemplateMergeSettings();

        $testTemplateFilename = $this->getTestTemplateFilename();
        $tempTemplateFilename = $this->getTempTemplateFilename();
        $tempTemplateName     = basename($tempTemplateFilename);

        copy($testTemplateFilename, $tempTemplateFilename);

        $response = $this->reportingCloud->uploadTemplate($tempTemplateFilename);

        $this->assertTrue($response);

        unlink($tempTemplateFilename);

        $response = $this->reportingCloud->mergeDocument($mergeData, 'PDF', $tempTemplateName, null, false, $mergeSettings);

        $this->assertNotNull($response);

        $this->assertNotFalse($response);

        $this->assertArrayHasKey(0, $response);
        $this->assertArrayHasKey(1, $response);
        $this->assertArrayHasKey(2, $response);
        $this->assertArrayHasKey(3, $response);
        $this->assertArrayHasKey(4, $response);

        $this->assertTrue(mb_strlen($response[0]) > 1024);

        $response = $this->reportingCloud->deleteTemplate($tempTemplateName);

        $this->assertTrue($response);
    }

    public function testMergeWithTemplateFilename()
    {
        $mergeData            = $this->getTestTemplateMergeData();
        $mergeSettings        = $this->getTestTemplateMergeSettings();

        $testTemplateFilename = $this->getTestTemplateFilename();

        $response = $this->reportingCloud->mergeDocument($mergeData, 'PDF', null, $testTemplateFilename, false, $mergeSettings);

        $this->assertNotNull($response);

        $this->assertNotFalse($response);

        $this->assertArrayHasKey(0, $response);
        $this->assertArrayHasKey(1, $response);
        $this->assertArrayHasKey(2, $response);
        $this->assertArrayHasKey(3, $response);
        $this->assertArrayHasKey(4, $response);

        $this->assertTrue(mb_strlen($response[0]) > 1024);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUploadTemplate_InvalidTemplateFilename()
    {
        $this->reportingCloud->uploadTemplate('/invalid/path/template.doc');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDownloadTemplate_InvalidTemplateName()
    {
        $templateFilename = $this->getTestTemplateFilename();   // should be templateName and not templateFilename

        $this->reportingCloud->downloadTemplate($templateFilename);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDeleteTemplate_InvalidTemplateName()
    {
        $templateFilename = $this->getTestTemplateFilename();   // should be templateName and not templateFilename

        $this->reportingCloud->deleteTemplate($templateFilename);
    }

    public function testUploadDownloadDeleteTemplate()
    {
        $testTemplateFilename = $this->getTestTemplateFilename();
        $tempTemplateFilename = $this->getTempTemplateFilename();
        $tempTemplateName     = basename($tempTemplateFilename);

        copy($testTemplateFilename, $tempTemplateFilename);

        $response = $this->reportingCloud->uploadTemplate($tempTemplateFilename);

        $this->assertTrue($response);

        $response = $this->reportingCloud->downloadTemplate($tempTemplateName);
        $responseLength = mb_strlen($response);

        $this->assertNotNull($response);
        $this->assertNotFalse($response);
        $this->assertTrue($responseLength > 1024);

        $response = $this->reportingCloud->deleteTemplate($tempTemplateName);

        $this->assertTrue($response);

        unlink($tempTemplateFilename);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @expectedException RuntimeException
     */
    public function testRequestRuntimeException()
    {
        $this->reportingCloud->deleteTemplate('invalid-template.tx');
    }

    // -----------------------------------------------------------------------------------------------------------------

    protected function getTestTemplateMergeData()
    {
        $mergeData = [
            0 => [
                'yourcompany_companyname' => 'Text Control, LLC',
                'yourcompany_zip' => '28226',
                'yourcompany_city' => 'Charlotte',
                'yourcompany_street' => '6926 Shannon Willow Rd, Suite 400',
                'yourcompany_phone' => '704 544 7445',
                'yourcompany_fax' => '704-542-0936',
                'yourcompany_url' => 'www.textcontrol.com',
                'yourcompany_email' => 'sales@textcontrol.com',
                'invoice_no' => '778723',
                'billto_name' => 'Joey Montana',
                'billto_companyname' => 'Montana, LLC',
                'billto_customerid' => '123',
                'billto_zip' => '27878',
                'billto_city' => 'Charlotte',
                'billto_street' => '1 Washington Dr',
                'billto_phone' => '887 267 3356',
                'payment_due' => '20/1/2016',
                'payment_terms' => 'NET 30',
                'salesperson_name' => 'Mark Frontier',
                'delivery_date' => '20/1/2016',
                'delivery_method' => 'Ground',
                'delivery_method_terms' => 'NET 30',
                'recipient_name' => 'Joey Montana',
                'recipient_companyname' => 'Montana, LLC',
                'recipient_zip' => '27878',
                'recipient_city' => 'Charlotte',
                'recipient_street' => '1 Washington Dr',
                'recipient_phone' => '887 267 3356',
                'item' => [
                    0 => [
                        'qty' => '1',
                        'item_no' => '1',
                        'item_description' => 'Item description 1',
                        'item_unitprice' => '2663',
                        'item_discount' => '20',
                        'item_total' => '2130.40',
                    ],
                    1 => [
                        'qty' => '1',
                        'item_no' => '2',
                        'item_description' => 'Item description 2',
                        'item_unitprice' => '5543',
                        'item_discount' => '0',
                        'item_total' => '5543',
                    ],
                ],
                'total_discount' => '532.60',
                'total_sub' => '7673.4',
                'total_tax' => '537.138',
                'total' => '8210.538',
            ],
        ];

        // copy data 4 times
        // total record sets = 5

        for ($i = 0; $i < 4; $i++) {
            array_push($mergeData, $mergeData[0]);
        }

        return $mergeData;
    }


    protected function getTestTemplateMergeSettings()
    {
        $mergeSettings = [

            'creation_date'              => time(),
            'last_modification_date'     => time(),

            'remove_empty_blocks'        => true,
            'remove_empty_fields'        => true,
            'remove_empty_images'        => true,
            'remove_trailing_whitespace' => true,

            'author'                     => 'James Henry Trotter',
            'creator_application'        => 'The Giant Peach',
            'document_subject'           => 'The Old Green Grasshopper',
            'document_title'             => 'James and the Giant Peach',

            'user_password'              => '123456789',
        ];

        return $mergeSettings;
    }

    protected function getTestTemplateFilename()
    {
        $ret = sprintf('%s/test_template.tx', realpath(__DIR__ . '/TestAsset'));
        return $ret;
    }


    protected function getTempTemplateFilename()
    {
        $ret = sprintf('%s/test_template_%d.tx', sys_get_temp_dir(), rand(0, PHP_INT_MAX));
        return $ret;
    }

    protected function getTestDocumentFilename()
    {
        $ret = sprintf('%s/test_document.docx', realpath(__DIR__ . '/TestAsset'));
        return $ret;
    }

    protected function getTempDocumentFilename()
    {
        $ret = sprintf('%s/test_document_%d.docx', sys_get_temp_dir(), rand(0, PHP_INT_MAX));
        return $ret;
    }

    // -----------------------------------------------------------------------------------------------------------------

}
