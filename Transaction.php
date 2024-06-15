<?php
class Transaction
{
    private ?string $transactionId = null;
    private ?string $timestamp = null;
    private DateTime $transactionDate;
    private ?string $iban = null;
    private ?string $type = null;
    private ?string $currency = null;
    private float $amount;
    private float $balanceAfter;
    private ?string $title = null;
    private ?string $contact = null;
    private ?string $address = null;
    private DateTime $currencyDate;
    private ?DateTime $elixirDate = null;
    private ?string $swrk = null;

    // Constructor
    public function __construct() { }

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
            'transactionId' => $this->transactionId,
            'timestamp' => $this->transactionDate->getTimestamp(),
            'transactionDate' => $this->transactionDate->format('Y-m-d'),
            'iban' => $this->iban,
            'type' => $this->type,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'balanceAfter' => $this->balanceAfter,
            'title' => $this->title,
            'contact' => $this->contact,
            'address' => $this->address,
            'currencyDate' => $this->currencyDate->format('Y-m-d'),
            'elixirDate' => $this->elixirDate->format('Y-m-d'),
            'swrk' => $this->swrk
        ];

        return json_encode($data);
    }
}
