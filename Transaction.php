<?php
class Transaction
{
    private $transactionDate;
    private $transactionAmount;
    private $iban;
    private $description;
    private $type;

    private $sepaReference;

    // Constructor
    public function __construct(DateTime $transactionDate, float $transactionAmount, ?string $iban, string $description, string $type, ?string $sepaReference)
    {
        $this->transactionDate = $transactionDate->format('Y-m-d');
        $this->transactionAmount = $transactionAmount;
        $this->iban = $iban;
        $this->description = $description;
        $this->type = $type;
        $this->sepaReference = $sepaReference;
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
            'transactionDate' => $this->transactionDate,
            'transactionAmount' => $this->transactionAmount,
            'iban' => $this->iban,
            'description' => $this->description,
            'sepaReference' => $this->sepaReference,
        ];

        return json_encode($data);
    }
}
?>