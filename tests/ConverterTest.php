<?php
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../converter/IConverter.php');
require_once(__DIR__ . '/../converter/Converter.php');
require_once(__DIR__ . '/../converter/ConverterFactory.php');
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

        $this->converter->convert('nonexistent_file.txt', 'output.csv');
    }

    public function testConvert()
    {
        $result = $this->converter->convert(__DIR__ . '/test_file.mta', 'output.txt');
        $this->assertEquals(10, sizeof($result));
        $this->assertEquals(floatval("100000.00"), floatval($result[0]->getTransactionAmount()));
        $this->assertEquals(floatval("-200000,00"), floatval($result[1]->getTransactionAmount()));
        $this->assertEquals("NL88RABO7959494258", $result[7]->getIBAN());
        $this->assertEquals("REDEMPTION OF BONDS TAN: xxxxxx IBAN: NL74RABO7959494258\n", $result[6]->getDescription());
    }
}
?>