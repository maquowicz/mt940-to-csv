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

        print_r($data[0]);
        // Open the CSV file for writing
        $handle = fopen($filename, 'w');

        // Write the headers to the CSV file
        $headers = array_keys(get_object_vars($data[0]));
        fputcsv($handle, $headers);

        // Write each object's properties to the CSV file
        foreach ($data as $obj) {
            $row = [];
            foreach ($headers as $header) {
                $row[] = $obj->{$header};
            }
            fputcsv($handle, $row);
        }

        // Close the CSV file
        fclose($handle);

        return true;
    }
}
?>