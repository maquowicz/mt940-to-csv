<?php
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../converter/IConverter.php');
require_once(__DIR__ . '/../converter/Converter.php');
require_once(__DIR__ . '/../converter/ConverterFactory.php');
require_once(__DIR__ . '/../converter/ConverterHelper.php');
require_once(__DIR__ . '/../converter/IConverterHelper.php');
require_once(__DIR__ . '/../exception/FileNotFoundException.php');
require_once(__DIR__ . '/../Transaction.php');
require_once(__DIR__ . '/../file/TextFileHandler.php');

class TextFileHandlerTest extends TestCase
{
    private $converter;

    private $textFileHandler;

    protected function setUp(): void
    {
        $this->converter = new Converter();
        $this->textFileHandler = new TextFileHandler();
    }

    public function testWriteSuccesfully()
    {
        $converted = $this->converter->convert(__DIR__ . '/test_file.mta');
        $result = $this->textFileHandler->writeObjectsToCsv($converted, __DIR__ . '/output.csv');
        $this->assertTrue($result);
    }

    public function testWriteNoData()
    {
        $result = $this->textFileHandler->writeObjectsToCsv(array(), __DIR__ . '/output.csv');
        $this->assertFalse($result);
    }
}
?>