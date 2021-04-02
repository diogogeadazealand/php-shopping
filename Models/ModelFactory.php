<?php

require_once( dirname(__FILE__) . "/../constants.php");

class ModelFactory{

    public static function __callstatic($class, $arguments = []){

        if(!file_exists(dirname(__FILE__)."/$class.php")){
            throw new Exception("File $class not found in Models diretory");
        }

        require_once(dirname(__FILE__)."/$class.php");
        return new $class(USER, PASSWORD, DATABASE);
    }

}