<?php
interface IConverter {
    /**
     * Convert a given input file to an array of rows
     *
     * @param string $input The input file to be converted
     * @param string $output The output file to save the converted data
     * 
     * @throws FileNotFoundException If the input file is not found on the system
     *
     * @return array The array of converted rows
     */
    public function convert(string $input, string $output): array;
}
?>