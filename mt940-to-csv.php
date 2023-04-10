<?php
require_once('./converter/IConverter.php');
require_once('./converter/Converter.php');
require_once('./converter/ConverterFactory.php');
require_once('./exception/FileNotFoundException.php');

$inputFile = 'Umsaetze_436726009_08.04.2023.mta'; // input MT940 file
$outputFile = 'output.csv'; // output CSV file

$factory = new ConverterFactory();
$converter = $factory->getConverter();
print_r($converter->convert($inputFile, $outputFile));