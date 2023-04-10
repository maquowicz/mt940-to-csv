<?php
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../converter/IConverter.php');
require_once(__DIR__ . '/../converter/Converter.php');
require_once(__DIR__ . '/../converter/ConverterFactory.php');
require_once(__DIR__ . '/../exception/FileNotFoundException.php');

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
        $expected = '[{"date":"2023-06-25","amount":"100000.00","iban":null,"description":"INVESTMENT INTERESTS PAID\n"},{"date":"2023-06-25","amount":-200000,"iban":null,"description":"REDEMPTION OF BONDS\n"},{"date":"2023-06-25","amount":-300000,"iban":null,"description":"STOCK PURCHASE FROM ABC COMPANY\n"},{"date":"2023-06-25","amount":"400000.00","iban":null,"description":"DEPOSIT INTO ACCOUNT FROM XYZ COMPANY\n"},{"date":"2023-06-25","amount":"500000.00","iban":null,"description":"PAYMENT FROM CUSTOMER FOR SERVICES RENDERED\n\/\/The statement trailer (62F) indicates the closing balance of the account\n"},{"date":"2023-06-25","amount":"100000.00","iban":null,"description":"INVESTMENT INTERESTS PAID TAN: 789308 IBAN: NL74RABO7959494258\n"},{"date":"2023-06-25","amount":-200000,"iban":null,"description":"REDEMPTION OF BONDS TAN: 789728 IBAN: NL74RABO7959494258\n"},{"date":"2023-06-25","amount":-300000,"iban":null,"description":"STOCK PURCHASE FROM ABC COMPANY TAN: 788728 IBAN: NL88RABO7959494258\n"},{"date":"2023-06-25","amount":"400000.00","iban":null,"description":"DEPOSIT INTO ACCOUNT FROM XYZ COMPANY\n"},{"date":"2023-06-25","amount":"500000.00","iban":null,"description":"PAYMENT FROM CUSTOMER FOR SERVICES RENDERED\n\/\/The statement trailer (62F) indicates the closing balance of the account\n"}]';

        $this->assertEquals(10, sizeof($result));
        $this->assertEquals($expected, json_encode($result));
    }
}
?>