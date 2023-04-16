<?php
/**
 * Converter implementation
 */
class Converter implements IConverter
{
    private array $rows = [];

    public function __construct()
    {
    }

    
    /**
     * Convert the input MT940 file to a list of transactions.
     *
     * @param  string $input The path to the input file name
     * @return array The list of transactions
     */
    public function convert(string $input): array
    {
        if (!file_exists($input)) {
            throw new FileNotFoundException();
        }

        $fileContents = file_get_contents($input);
        $transactions = $this->extractTransactions($fileContents);
        $this->rows = $this->convertToRows($transactions);
        return $this->rows;
    }

    /**
     * Extract transactions from the given file contents using regular expressions.
     *
     * @param string $fileContents The contents of the input file
     * @return array The list of transactions
     */
    private function extractTransactions(string $fileContents): array
    {
        preg_match_all('/(?<=:61:).*?(?=:[0-9]{2}[A-Z]{0,1}:)|(?<=:86:).*?(?=:[0-9]{2}[A-Z]{0,1}:)/s', $fileContents, $matches);
        return $matches[0];
    }

    /**
     * Convert a list of transactions to rows.
     *
     * @param array $transactions The list of transactions
     * @return array The list of rows
     */
    private function convertToRows(array $transactions): array
    {
        $rows = [];
        foreach (array_chunk($transactions, 2) as [$transaction, $description]) {
            $row = $this->convertToRow($transaction, $description);
            if ($row !== null) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    /**
     * Convert a transaction to a row.
     *
     * @param string $transaction The transaction to convert
     * @param string $description The description of the transaction
     * @return Transaction|null The converted row or null if an error occurred
     */
    private function convertToRow(string $transaction, string $description): ?Transaction
    {
        preg_match('/(\d{6})(\d{4})?([A-Z])([A-Z]{1,2})?(\d+,\d+)?/', $transaction, $matches);
        if (sizeof($matches) !== 6) {
            echo "Error in parsing line " . $transaction;
            return null;
        }

        $transactionDate = $matches[1];
        $date = DateTime::createFromFormat('ymd', $transactionDate);

        $transactionAmount = str_replace(',', '.', $matches[5]);
        $type = $matches[3];
        if ($type === 'D') {
            $transactionAmount = -$transactionAmount;
        }

        $cleanedDescription = $this->cleanDescription($description);

        $memo = $this->getMemo($cleanedDescription);

        $iban = $this->getIBAN($memo);

        $sepa = $this->getSEPAMandateReference($memo);

        return new Transaction($date, $transactionAmount, $iban, rtrim($memo), $type, $sepa);
    }

    /**
     * Cleans the given description by removing any special characters and replacing the TAN number with "xxxxxx".
     * 
     * @param string $description The description to be cleaned
     * @return string The cleaned description
    */
    private function cleanDescription($description) : string{
        //$cleanedDescription = preg_replace('/\?[0-9]{2}/', '', $description);
        $cleanedDescription = preg_replace('/TAN: (\d{6})/', 'TAN: xxxxxx', $description);
        $cleanedDescription = preg_replace('/\R+/', '', $cleanedDescription);
        return $cleanedDescription;
    }

        
    /**
     * Gets the SEPA mandate reference out of the description
     *
     * @param  string $description The description or text to extract the SEPA mandate reference
     * @return string The extracted SEPA reference or NULL
     */
    private function getSEPAMandateReference(string $description) : ?string {
        if($description === null || trim($description) === "") {
            return null;
        }
        $matches = array();
        preg_match("/\b([A-Z]{2}\d{2}[A-Z0-9]{3}\d{1,30})\b/", $description, $matches);
        if(sizeof($matches) <= 0){
            return null;
        }
        return $matches[1];
    }

        
    /**
     * Get the IBAN out of the bank text
     *
     * @param  string $description The description or text to extract the IBAN from
     * @return string The extracted IBAN or null
     */
    private function getIBAN(string $description) : ?string{
        preg_match('/IBAN: ([A-Z]{2}\d{2}[A-Z0-9]{14})/', $description, $ibanMatches);
        if (sizeof($ibanMatches) === 2) {
            return $ibanMatches[1];
        }
        return null; 
    }

    private function getPostingText(string $description) : ?string{
        preg_match('/\?00([\w\d]{1,27})\?/', $description, $matches);
        if (sizeof($matches) === 2) {
            return $matches[1];
        }
        return null; 
    }

    private function getMemo(string $description) : ?string {
        //First filter everything between the ?20 and the ?30
        preg_match('/\?20(.*)\?30/', $description, $initialMatches);

        //No match => Does not have ?20 in it => Return the string
        if (sizeof($initialMatches) === 0) {
            return $description;
        }

        //Then check if there is a letter before the ? and a letter behind the ? => no space
        //Then check if there is a number before the ? and a letter behind the ? => no space
        //Then check if there is a letter before the ? and a number behind the ? => space
        //Then check if there is a number before the ? and a letter behind the ? => space
        if (sizeof($initialMatches) === 2) {
            $workingString = $initialMatches[1];
            $workingString = preg_replace('/([a-zA-Z]{1})\?2\d([a-zA-Z]{1})/', '$1$2', $workingString);
            $workingString = preg_replace('/([0-9]{1})\?2\d([0-9]{1})/', '$1$2', $workingString);
            $workingString = preg_replace('/([a-zA-Z]{1})\?2\d([0-9]{1})/', '$1 $2', $workingString);
            $workingString = preg_replace('/([0-9]{1})?\?2\d([a-zA-Z]{1})/', '$1 $2', $workingString);
            $workingString = preg_replace('/([a-z]{1})\?2\d([A-Z]{1})/', '$1 $2', $workingString);
            $workingString = preg_replace('/(\s{1})\?2\d([0-9]{1})/', '$1$2', $workingString);
            $workingString = preg_replace('/(\s{1})?\?2\d([a-zA-Z]{1})/', '$1$2', $workingString);
            $workingString = preg_replace('/([a-zA-Z]{1})\?2\d(\s{1})/', '$1$2', $workingString);
            $workingString = preg_replace('/([0-9]{1})?\?2\d(\s{1})/', '$1$2', $workingString);
            return $workingString;   
        }
        return null; 
    }
}
