<?php

class Message{

    private $text;
    private $code;
    private $success;

    public function __get($var){
        return $this->$var;
    }

    public function SetText($text){
        $this->text = $text;
    }

    public function AddText($text){
        if($this->text == null || gettype($this->text) === "string"){
            $this->text = array();
        }
        $this->text[] = $text;
    }

    public function SetSuccess($value){
        $this->success = $value;
    }

    public function __construct($success, $text = "", $code =""){

        $this->text = $text;
        $this->success = $success;
        $this->code = $code;
    }

}