<?php
require_once(__DIR__ . '/../converter/IConverterHelper.php');
require_once(__DIR__ . '/../converter/ConverterHelper.php');

/**
 * @covers ConverterHelper
 */
class ConverterHelperTest extends \PHPUnit\Framework\TestCase
{
    private $converterHelper;

    protected function setUp(): void {
        $this->converterHelper = new ConverterHelper();
    }

    public function testCleanDescription() {
        $description = 'TAN: 123456 Test 123456 Special Characters _+ $%^ *()';
        $expectedResult = 'TAN: xxxxxx Test 123456 Special Characters _+ $%^ *()';
        $result = $this->converterHelper->cleanDescription($description);
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetSEPAMandateReference() {
        $description = 'SEPA Mandate Reference: NL38ING00001234567';
        $expectedResult = 'NL38ING00001234567';
        $result = $this->converterHelper->getSEPAMandateReference($description);
        $this->assertEquals($expectedResult, $result);

        $description = 'Payment for the rent';
        $result = $this->converterHelper->getSEPAMandateReference($description);
        $this->assertNull($result);

        $description = '';
        $result = $this->converterHelper->getSEPAMandateReference($description);
        $this->assertNull($result);
    }

    public function testGetIBAN() {
        $description = '?31NL23INGB01234567897898';
        $expectedResult = 'NL23INGB01234567897898';
        $result = $this->converterHelper->getIBAN($description);
        $this->assertEquals($expectedResult, $result);

        $description = 'Payment for the rent';
        $result = $this->converterHelper->getIBAN($description);
        $this->assertNull($result);

        $description = '';
        $result = $this->converterHelper->getIBAN($description);
        $this->assertNull($result);
    }

    public function testGetName() {
        $description = '?32JohnDoe?';
        $expectedResult = 'JohnDoe';
        $result = $this->converterHelper->getName($description);
        $this->assertEquals($expectedResult, $result);

        $description = '?32John?33Doe?';
        $expectedResult = 'JohnDoe';
        $result = $this->converterHelper->getName($description);
        $this->assertEquals($expectedResult, $result);

        $description = '?32JohnDoe?33TheSecond?';
        $expectedResult = 'JohnDoeTheSecond';
        $result = $this->converterHelper->getName($description);
        $this->assertEquals($expectedResult, $result);

        $description = 'Payment for the rent';
        $result = $this->converterHelper->getName($description);
        $this->assertNull($result);

        $description = '';
        $result = $this->converterHelper->getName($description);
        $this->assertNull($result);
    }

    public function testGetPostingText() {
        $description = "This is a sample description ?0011234567? with valid posting text";
        $this->assertEquals("11234567", $this->converterHelper->getPostingText($description));

        $description = "This is a sample description without any posting text";
        $this->assertNull($this->converterHelper->getPostingText($description));
    }

    public function testGetMemo() {
        $description = "This is a sample description ?20Sample Memo?30 with valid memo";
        $this->assertEquals("Sample Memo", $this->converterHelper->getMemo($description));

        $description = "This is a sample description without any memo";
        $this->assertNull($this->converterHelper->getMemo($description));

        $description = "This is a sample description ?20Sample_Memo/with_special+characters?30 with valid memo";
        $this->assertEquals("Sample_Memo/with_special+characters", $this->converterHelper->getMemo($description));

        $description = "This is a sample description ?20Memo/with/special/characters?30 with valid memo";
        $this->assertEquals("Memo/with/special/characters", $this->converterHelper->getMemo($description));

        $description = "This is a sample description ?20   Sample Memo?30 with valid memo";
        $this->assertEquals("   Sample Memo", $this->converterHelper->getMemo($description));

        $description = "This is a sample description ?20Sample Memo   ?30 with valid memo";
        $this->assertEquals("Sample Memo   ", $this->converterHelper->getMemo($description));

        $description = "This is a sample description ?20SampleMemo?30 with valid memo";
        $this->assertEquals("SampleMemo", $this->converterHelper->getMemo($description));

        $description = "This is a sample description ?20Sample Memo 1234?30 with valid memo";
        $this->assertEquals("Sample Memo 1234", $this->converterHelper->getMemo($description));
    }

}
