<?php
class FileNotFoundException extends Exception
{
    protected $message = 'File not found';
    protected $code = 404;
}

?>