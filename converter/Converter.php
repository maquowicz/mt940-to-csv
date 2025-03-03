<?php
/**
 * Converter implementation
 */
class Converter implements IConverter
{
    private array $rows = [];
    private string $currency;
    private string $balance;

    /** @var IConverterHelper|ConverterHelper  */
    private IConverterHelper $converterHelper;

    public function __construct()
    {
        $this->converterHelper = new ConverterHelper();
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
        $fileContents = mb_convert_encoding($fileContents, 'UTF-8',
            mb_detect_encoding($fileContents, 'UTF-8, ISO-8859-2', true)
        );

        preg_match('/(?<=:60F:)(C|D)(\d{6})([A-Z]{3})(.*)/', $fileContents,$matches);
        $this->currency = $matches[3];
        $matches[4] = (float)rtrim(str_replace(",", ".", $matches[4]), "\r");
        $this->balance = $matches[1] == "C" ? $matches[4] : -$matches[4];
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
        //$modifiedString = $this->cleanTime($fileContents);
        preg_match_all('/(?<=:61:).*?(?=:[\d]{2}[A-Z]{0,1}:)|(?<=:86:).*?(?=:[\d]{2}[A-Z]{0,1}:)/s', $fileContents, $matches);
        return $matches[0];
    }

    /**
     * Convert the : for the time to - so that it does not interfere with other regex patterns
     * 
     * @param string $input The input text
     * @return string The converted string
     */
    private function cleanTime(string $input){
        $pattern = '/(?<=\s)([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/';

        // Callback function to replace colons with dashes in the matched time string
        $output = preg_replace_callback($pattern, function($matches) {
            return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
        }, $input);

        return $output;
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
        //preg_match('/(\d{6})(\d{4})?([A-Z])([A-Z]{1,2})?(\d+,\d+)?/', $transaction, $matches);
        preg_match('/(\d{6})(\d{4})?([A-Z])([A-Z]{1,2})?(\d+,\d+)N(.{3})NONREF\/\/(\d{8})(\d{8})?/', $transaction, $matches);
        if (sizeof($matches) !== 9) {
            echo "Error in parsing line " . $transaction;
            return null;
        }

        $currencyDateRaw = $matches[1];
        $currencyDate = DateTime::createFromFormat('ymd', $currencyDateRaw);

        $txDateRaw = $currencyDate->format('y').$matches[2]; // this is missing year
        $txDate = DateTime::createFromFormat('ymd', $txDateRaw);

        $txType = $matches[3];
        $amount = str_replace(',', '.', $matches[5]);
        $details = $this->converterHelper->cleanDescription($description);

        $ozsiCode = $this->converterHelper->getOpCode($details);
        if($ozsiCode != $matches[6]) {
            echo "O-ZSI Type mismatch. " . $transaction;
            return null;
        }
        $title = $this->converterHelper->getTitle($details);
        $iban = $this->converterHelper->getIBAN($details, $ozsiCode);
        $contact = $this->converterHelper->getContact($details, $ozsiCode);
        $address = $this->converterHelper->getAddress($details, $ozsiCode);
        $swrk = $this->converterHelper->getSwrk($details);
        $elixirDate = $this->converterHelper->getElixirDate($details);

        $amount = $txType == "C" ? $amount : -$amount;
        $this->balance += $amount;

        // build result obj
        $trx = new Transaction();
        $trx->transactionId = hash('crc32', $txDate->format('Y-m-d') . $matches[7] . $matches[8] . $iban . $amount);
        $trx->transactionNumber = $matches[7] . '/' . $matches[8];
        $trx->currencyDate = $currencyDate;
        $trx->currency = $this->currency;
        $trx->transactionDate = $txDate;
        $trx->timestamp = $txDate->getTimestamp();
        $trx->ozsiCode = $ozsiCode;
        $trx->amount = $amount;
        $trx->balanceAfter = $this->balance;
        $trx->title = $title;
        $trx->iban = $iban;
        $trx->contact = $contact;
        $trx->address = $address;
        $trx->swrk = $swrk;
        $trx->elixirDate = $elixirDate ?: null;
        $trx->type = $txType;

        return $trx;
    }
}
