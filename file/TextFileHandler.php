<?php
class TextFileHandler
{
    public function __construct()
    {
    }

    public function read($file)
    {
        return file_get_contents($file);
    }

    public function write($file, $content)
    {
        file_put_contents($file, $content);
    }

    /**
     * Write an array of objects to a CSV file.
     * The variable names of the objects will be used as headers.
     *
     * @param array $data An array of objects to write to the CSV file
     * @param string $filename The name of the CSV file to write to
     * @return bool Returns true if the write was successful, false otherwise
     */
    public function writeObjectsToCsv(array $data, string $filename): bool
    {
        if (empty($data)) {
            return false;
        }

        // Open the CSV file for writing
        $handle = fopen($filename, 'w');

        //TODO: It might be better to pass the headers over using reflection here. The idea behind this is to make it as easy as possible for the user. On the other, there is no other chance then to use the variable names in the csv file as well.
        $reflectionClass = new ReflectionClass($data[0]);
        $reflectionProperties = $reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE);

        $headers = array();

        foreach($reflectionProperties as $reflectionProperty){
            array_push($headers, $reflectionProperty->getName());
        }
        
        fputcsv($handle, $headers);

        // Write each object's properties to the CSV file
        foreach ($data as $obj) {
            $row = [];
            foreach ($headers as $header) {
                if(is_a($obj->{$header}, 'DateTime')){
                    $row[] = $obj->{$header}->format('Y-m-d');
                }else{
                    $row[] = $obj->{$header};
                }   
            }
            fputcsv($handle, $row);
        }

        // Close the CSV file
        fclose($handle);

        return true;
    }
}
?>