<?php
require_once('./converter/IConverter.php');
require_once('./converter/Converter.php');
require_once('./converter/ConverterFactory.php');
require_once('./exception/FileNotFoundException.php');
require_once('./file/TextFileHandler.php');
require_once('./Transaction.php');

/**
 * Two parameters need to be given
 * i: The path to the input file
 * o: The path to the output file
 */
$options = getopt("i:o:h", ["input:", "output:", "help"]);

if (isset($options['h']) || isset($options['help'])) {
    echo "Usage: php mta940-to-csv.php --input=<filename> --output=<filename>\n";
    exit;
}

if (!isset($options['i']) || !isset($options['o'])) {
    echo "Error: You need to provide an input and an output file\n";
    echo "Usage: php mta940-to-csv.php --input=<filename> --output=<filename>\n";
    exit;
}

$inputFile = $options['i'];
$outputFile = $options['o'];

$factory = new ConverterFactory();
$converter = $factory->getConverter();
$converted = $converter->convert($inputFile, $outputFile);

$textFileHandler = new TextFileHandler();
$textFileHandler->writeObjectsToCsv($converted, $outputFile);
