<?php
require('./Transaction.php');
require('./Converter.php');

$inputFile = 'Umsaetze_436726009_08.04.2023.mta'; // input MT940 file
$outputFile = 'output.csv'; // output CSV file

$converter = new Converter();
print_r($converter->convert($inputFile, $outputFile));

// Open the input file and read its contents

// Open the output file and write the CSV data
/*$outputHandle = fopen($outputFile, 'w');
foreach ($rows as $row) {
    fputcsv($outputHandle, $row);
}
fclose($outputHandle);
*/