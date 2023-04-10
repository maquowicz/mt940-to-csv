<?php
class ConverterFactory{

    public function getConverter() : IConverter{
        return new Converter();
    }

}
?>