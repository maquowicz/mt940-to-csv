<?php
/**
 * An interface for a converter helper class to do the heavy lifting of the converter tasks
 */
interface IConverterHelper {
    
    /**
     * Cleans the given description by removing any special characters and replacing the TAN number with "xxxxxx".
     * 
     * @param string $description The description to be cleaned
     * @return string The cleaned description
     */
    public function cleanDescription(string $description): string;
    
    /**
     * Gets the SEPA mandate reference out of the description
     *
     * @param  string|null $description The description or text to extract the SEPA mandate reference
     * @return string|null The extracted SEPA reference or NULL
     */
    public function getSEPAMandateReference(?string $description): ?string;
    
    /**
     * Get the IBAN out of the bank text
     *
     * @param  string $description The description or text to extract the IBAN from
     * @return string|null The extracted IBAN or null
     */
    public function getIBAN(string $description): ?string;
    
    /**
     * Get the name of the principal
     *
     * @param  string $description The description or text to extract the name from
     * @return string|null The name of the principal or null
     */
    public function getName(string $description): ?string;
    
    /**
     * Get the posting text
     *
     * @param  string $description The description or text to extract the posting text from
     * @return string|null The extracted posting text or null
     */
    public function getPostingText(string $description): ?string;
    
    /**
     * Get the memo
     *
     * @param  string $description The description or text to extract the memo from
     * @return string|null The extracted memo or null
     */
    public function getMemo(string $description): ?string;
}

?>