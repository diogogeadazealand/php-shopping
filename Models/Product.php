<?php

require_once( dirname(__FILE__) . "/../Classes/Database.php");
require_once( dirname(__FILE__) . "/../Classes/Validator.php");

class Product extends Database{

    public $id;
    public $name;
    public $image;
    public $description;
    public $price;
    public $category_id;
    protected $table = "products";

    public function Setup(){
        $this->validation = array(
            "name" => Validator::TEXT,
            "price" => Validator::NUMBER,
            "category_id" => Validator::ID,
        );
    }

    public function Format($array){
        $this->id = (isset($array["id"])) ? $array["id"] : -1;
        $this->name = (isset($array["name"])) ? $array["name"] : "";
        $this->image = (isset($array["image"])) ? $array["image"] : "";
        $this->description = (isset($array["description"])) ? $array["description"] : "";
        $this->price = (isset($array["price"])) ? $array["price"] : "";
        $this->category_id = (isset($array["category_id"])) ? $array["category_id"] : "";
    }

}