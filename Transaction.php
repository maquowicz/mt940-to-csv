<?php
class Transaction
{
    private DateTime $transactionDate;
    private float $transactionAmount;
    private ?string $payerIban = null;
    private ?string $payerName = null;
    private ?string $recepientIban = null;
    private ?string $recipientName = null;
    private ?string $description = null;
    private ?string $type = null;
    private ?string $sepaReference = null;

    // Constructor
    public function __construct()
    {
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    // Convert the object to JSON format
    public function toJson()
    {
        $data = [
            'transactionDate' => $this->transactionDate->format('Y-m-d'),
            'transactionAmount' => $this->transactionAmount,
            'payerIban' => $this->payerIban,
            'payerName' => $this->payerName,
            'recipientIban' => $this->recipientIban,
            'recipientName' => $this->recipientName,
            'description' => $this->description,
            'type' => $this->type,
            'sepaReference' => $this->sepaReference,
        ];

        return json_encode($data);
    }
}
?>