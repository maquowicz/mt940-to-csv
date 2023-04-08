<?php
class Transaction{
    private $_transactionDate;
    private $_transactionAmount;
   private $_iban;

   private $_description;

    // Constructor
    public function __construct($transactionDate, $transactionAmount){
        $this->_transactionDate = $transactionDate;
        $this->_transactionAmount = $transactionAmount;
    }

    // Getters and Setters
    public function getTransactionDate(){
        return $this->_transactionDate;
    }

    public function setTransactionDate($transactionDate){
        $this->_transactionDate = $transactionDate;
    }

    public function getTransactionAmount(){
        return $this->_transactionAmount;
    }

    public function setTransactionAmount($transactionAmount){
        $this->_transactionAmount = $transactionAmount;
    }

    public function getIBAN(){
        return $this->_iban;
    }

    public function setIBAN($iban){
        $this->_iban = $iban;
    }

    public function getDescription(){
        return $this->_description;
    }

    public function setDescription($description){
        $this->_description = $description;
    }


    // Display transaction information
    public function displayTransaction(){
        echo "Transaction Date: " . $this->_transactionDate . "<br>";
        echo "Transaction Amount: " . $this->_transactionAmount . "<br>";
    }
}
?>
