<?php
require('./Transaction.php');

$inputFile = 'Umsaetze_436726009_08.04.2023.mta'; // input MT940 file
$outputFile = 'output.csv'; // output CSV file

// Open the input file and read its contents
$fileContents = file_get_contents($inputFile);

//Remove all line breaks
$no_lb = str_replace("\r\n", "", $fileContents);

//Filter all :61: and :86: of the content as these contain the transaction details necessary
//It always comes as a pair [index]=:61:, [index+1]=:86:
//TODO: Refactor this, there must be a better way
preg_match_all('/(?<=:61:).*?(?=:[0-9]{2}[A-Z]{0,1}:)|(?<=:86:).*?(?=:[0-9]{2}[A-Z]{0,1}:)/s', $no_lb, $matches);
// Create an empty array to store the CSV rows
$rows = array();
$transactions = $matches[0];
$size_of_transactions = sizeof($transactions);
$index = 0;

$current_trx = null;
while ($index < $size_of_transactions) {
    $current = $transactions[$index];
    if ($index % 2 == 0) {
        //:61:
        preg_match('/(\d{6})(\d{4})([A-Z]{1})([A-Z]{1,2})?(\d+,\d+)?/', $current, $matches);
        //Exactly 6 matches are needed, otherwise continue
        if (sizeof($matches) != 6) {
            echo "Error in parsing line " . $current;
            die();
        }

        $transactionDate = $matches[1];
        $date = DateTime::createFromFormat('ymd', $transactionDate);

        $transactionAmount = str_replace(',', '.', $matches[5]);
        if ($matches[3] === 'D') {
            $transactionAmount = -$transactionAmount; // Convert debit amounts to negative numbers
        }
        $current_trx = new Transaction($date->format('Y-m-d'), $transactionAmount);
        $index++;
    } else {
        if($current_trx == null){
            print($index);
        }
        //:81:
        $iban = null;
        $cleaned_current = preg_replace('/\?[0-9]{2}/','', $current);
        preg_match('/IBAN: ([A-Z]{2}[0-9]{18})/', $cleaned_current, $iban_matches);
        if(sizeof($iban_matches) == 2){
            $iban = $iban_matches[1];
        }
        $current_trx->setIBAN($iban);
        $current_trx->setDescription($cleaned_current);
        array_push($rows, $current_trx);
        $current_trx = null;
        $index++;
    }
}
print_r($rows);
// Open the output file and write the CSV data
/*$outputHandle = fopen($outputFile, 'w');
foreach ($rows as $row) {
    fputcsv($outputHandle, $row);
}
fclose($outputHandle);
*/