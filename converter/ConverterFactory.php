<?php
require_once('./IConverter.php');
require_once('./exception/FileNotFoundException.php');

class ConverterFactory{

    public function getConverter() : IConverter{
        return new Converter();
    }

}
?>