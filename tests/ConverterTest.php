<?php
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../converter/IConverter.php');
require_once(__DIR__ . '/../converter/Converter.php');
require_once(__DIR__ . '/../converter/ConverterFactory.php');
require_once(__DIR__ . '/../converter/ConverterHelper.php');
require_once(__DIR__ . '/../converter/IConverterHelper.php');
require_once(__DIR__ . '/../exception/FileNotFoundException.php');
require_once(__DIR__ . '/../Transaction.php');

class ConverterTest extends TestCase
{
    private $converter;

    protected function setUp(): void
    {
        $this->converter = new Converter();
    }

    public function testConvertThrowsFileNotFoundException()
    {
        $this->expectException(FileNotFoundException::class);

        $this->converter->convert('nonexistent_file.txt');
    }

    public function testConvert()
    {
        $result = $this->converter->convert(__DIR__ . '/test_file.mta');
        $this->assertEquals(10, sizeof($result));
        $this->assertEquals("EREF+xx:xxxxxxxxKREF+2021122986876852591700000000032013 MREF+xxxxxxxxCRED+xxxxxxxxxxxxxxxSVWZ+xxxxxxxx XXXXXXXXungsnummer XXXX-XXX-XXXX XXEF: xx:xxxxxxxx MREF: xxxxxx 13 CRED: xxxxxxxxxxxxxxxx IBAN: XXXXXXXXXXXXXXXXXX", $result[0]->description);
        $this->assertEquals(floatval("100000.00"), floatval($result[0]->transactionAmount));
        $this->assertEquals(floatval("-200000,00"), floatval($result[1]->transactionAmount));
        $this->assertEquals("NL00000600000XXXX00000", $result[0]->payerIban);
        $this->assertEquals("Insurance-AG", $result[0]->payerName);
        $this->assertEquals("Insurance", $result[1]->recipientName);
        $this->assertNull($result[7]->payerIban);
        $this->assertEquals("DE98ZZZ09999999999", $result[7]->sepaReference);
        $this->assertEquals("REDEMPTION OF BONDS TAN: xxxxxx IBAN: NL74RABO7959494258 GB29NWBK60161331926819", $result[6]->description);
    }
}
