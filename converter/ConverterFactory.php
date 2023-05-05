<?php
/**
* Factory class for creating instances of the Converter class.
*/
class ConverterFactory{

    /**
    * Returns an instance of the Converter class
    * 
    * @return IConverter An instance of the Converter class.
    */
    public function getConverter() : IConverter{
        return new Converter();
    }

}
