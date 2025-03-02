<?php
/**
 * Helper class to do the heavy lifting of the converter tasks
 */
class ConverterHelper implements IConverterHelper
{

    public function __construct()
    {
        //Empty constructor
    }

    /**
     * Cleans the given description by removing any special characters and replacing the TAN number with "xxxxxx".
     *
     * @param string $description The description to be cleaned
     * @return string The cleaned description
     */
    public function cleanDescription($description): string
    {
        //$cleanedDescription = preg_replace('/TAN: (\d{6})/', 'TAN: xxxxxx', $description);
        //$cleanedDescription = preg_replace('/\R+/', '', $cleanedDescription);
        $result = str_replace("˙", "", $description);

        return $result;
    }


    /**
     * Gets the SEPA mandate reference out of the description
     *
     * @param string $description The description or text to extract the SEPA mandate reference
     * @return string The extracted SEPA reference or NULL
     */
    public function getSEPAMandateReference(?string $description): ?string
    {
        if ($description === null || trim($description) === "") {
            return null;
        }
        $matches = array();
        preg_match("/\b([A-Z]{2}\d{2}[A-Z0-9]{3}\d{1,30})\b/", $description, $matches);
        if (empty($matches)) {
            return null;
        }
        return $matches[1];
    }


    /**
     * Get the IBAN out of the bank text
     *
     * @param string $string The description or text to extract the IBAN from
     * @return string The extracted IBAN or null
     */
    public function getIBAN(string $string): ?string
    {
        $tString = explode("\n", $string);
        $result = "";
        foreach ($tString as $line) {
            if (str_starts_with($line, "~38")) {
                $result = substr($line, 3, strlen($line));
            }
        }
        return str_replace("\r", "", $result);
    }

    /**
     * Get the name of the principal
     *
     * @param string $description The description or text to extract the name from
     * @return string The name of the principal or null
     */
    public function getName(string $description): ?string
    {
        $pattern = '/\?32((?:(?!\?33).)*?)(\?34|$)/'; // updated pattern to include optional part between ?33 and ?

        $description = str_replace('?33', '', $description);

        preg_match($pattern, $description, $matches);

        if (isset($matches[1])) {
            $result = $matches[1]; // contains the extracted substring before ?33

            $result = str_replace('?', '', $result); // remove any remaining question marks

            if (isset($matches[3])) {
                $optionalPart = $matches[3]; // contains the extracted substring between ?33 and ?
                $optionalPart = str_replace('?', '', $optionalPart); // remove any remaining question marks
                $result .= $optionalPart; // concatenate both parts
            }

            return $result;
        }

        return null;
    }

    public function getPostingText(string $description): ?string
    {
        preg_match('/\?00([\w\d]{1,27})\?/', $description, $matches);
        if (sizeof($matches) === 2) {
            return $matches[1];
        }
        return null;
    }

    public function getMemo(string $description): ?string
    {
        //First filter everything between the ?20 and the ?30
        preg_match('/\?20(.*)\?30/', $description, $initialMatches);

        //No match => Does not have ?20 in it => Return the string
        if (!$initialMatches) {
            return null;
        }

        /**
         * The following regex are based on these assumptions:
         * 1. If there is a character before the ? and a character after the ? it is one word and no space is added
         * 2. If there is a number before the ? and a number after the ? it is one word and no space is added
         * 3. If there is a character before the ? and a number after the ? it is two words and a space is added
         * 4. If there is a number before the ? and a character after the ? it is two words and a space is added
         * 5. If there is a lower case character before the ? and a upper case character after the ? it is two words and a space is added
         * 6. If there is space before the ? and a number after the ? it is directly combined as there is alredy a space
         * 7. If there is space before the ? and a character after the ? it is directly combined as there is alredy a space
         * 8. If there is character before the ? and a space after the ? it is directly combined as there is alredy a space
         * 9. If there is number before the ? and a space after the ? it is directly combined as there is alredy a space
         */
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

    public function getOpCode(string $string): ?string
    {
        //:86:020~00{opCode}
        $tString = explode("\n", $string);
        $result = str_replace("020~00", "", $tString[0]);
        return str_replace("\r", "", $result);
    }

    public function getTitle(string $string): ?string
    {
        $tString = explode("\n", $string);
        $lines = [20, 21, 22, 23, 24, 25];
        $result = "";
        foreach ($tString as $line) {
            if (str_starts_with($line, "~") && in_array((int)substr($line, 1, 2), $lines)) {
                $cLine = str_replace("\r", "", $line);
                if (!empty($cLine)){
                    $result .= substr($cLine, 3, strlen($cLine));
                }
            }
        }
        return $result;
    }

    public function getContact(string $string, $type = null): ?string
    {
        switch($type) {
            case 114: // card debit
                $startsWith = ['~22', '~23'];
                break;
            default:
                $startsWith = ['~32'];
                break;
        }
        $tString = explode("\n", $string);
        $result = "";
        foreach ($tString as $line) {
            if (in_array(substr($line, 0, 3), $startsWith)) {
                $result .= substr(trim($line), 3, strlen($line));
            }
        }
        return str_replace("\r", "", $result);
    }

    public function getAddress(string $string, $type = null): ?string
    {
        switch($type) {
            case 114: // card debit
                $startsWith = ['~22', '~23'];
                break;
            default:
                $startsWith = ['~32'];
                break;
        }
        $tString = explode("\n", $string);
        $result = "";
        foreach ($tString as $line) {
            if (str_starts_with($line, "~33")) {
                $result = substr($line, 3, strlen($line));
            }
        }
        return str_replace("\r", "", $result);
    }

    public function getSwrk(string $string): ?string
    {
        $tString = explode("\n", $string);
        $result = "";
        foreach ($tString as $line) {
            if (str_starts_with($line, "~63")) {
                $result = substr($line, 3, strlen($line));
            }
        }
        return str_replace("\r", "", $result);
    }

    public function getElixirDate(string $string)
    {
        $tString = explode("\n", $string);
        $result = "";
        foreach ($tString as $line) {
            if (str_starts_with($line, "~60")) {
                $result = substr($line, 3, strlen($line));
            }
        }

        $result = str_replace("\r", "", $result);
        $date = DateTime::createFromFormat('Ymd', $result);
        return $date;
    }
}
