<?php
class Transaction
{
    private $transactionDate;
    private $transactionAmount;
    private $iban;
    private $description;

    // Constructor
    public function __construct($transactionDate, $transactionAmount, $iban, $description)
    {
        $this->transactionDate = $transactionDate;
        $this->transactionAmount = $transactionAmount;
        $this->iban = $iban;
        $this->description = $description;
    }

    // Getters and Setters
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }

    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;
    }

    public function getTransactionAmount()
    {
        return $this->transactionAmount;
    }

    public function setTransactionAmount($transactionAmount)
    {
        $this->transactionAmount = $transactionAmount;
    }

    public function getIBAN()
    {
        return $this->iban;
    }

    public function setIBAN($iban)
    {
        $this->iban = $iban;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    // Convert the object to JSON format
    public function toJson()
    {
        $data = [
            'transactionDate' => $this->transactionDate,
            'transactionAmount' => $this->transactionAmount,
            'iban' => $this->iban,
            'description' => $this->description,
        ];

        return json_encode($data);
    }
}
?>