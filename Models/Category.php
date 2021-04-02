<?php

require_once( dirname(__FILE__) . "/../Classes/Database.php");

class Category extends Database{

    public $id;
    public $name;
    public $parent_id;
    protected $table = "categories";

    public function Format($array){
        $this->id = (isset($array["id"])) ? $array["id"] : -1;
        $this->name = (isset($array["name"])) ? $array["name"] : "";
        $this->parent_id = (isset($array["phone"])) ? $array["phone"] : "";
    }

    public function Fetch(){
        $categories = $this->Select();
        
        $result = array();

        foreach($categories as $category){
            if($category["parent_id"] == null){
                $result[$category["id"]] = $category;
            } else {
                $result[$category["parent_id"]]["children"][] = $category;
            }
        }

        return $result;
    }

}