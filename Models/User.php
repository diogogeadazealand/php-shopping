<?php

require_once( dirname(__FILE__) . "/../Classes/Database.php");

class User extends Database{

    public $id;
    public $name;
    public $phone;
    public $adress;
    public $email;
    public $is_admin;
    protected $table = "users";

    public function Format($array){
        $this->id = (isset($array["id"])) ? $array["id"] : -1;
        $this->name = (isset($array["name"])) ? $array["name"] : "";
        $this->phone = (isset($array["phone"])) ? $array["phone"] : "";
        $this->adress = (isset($array["adress"])) ? $array["adress"] : "";
        $this->email = (isset($array["email"])) ? $array["email"] : "";
        $this->is_admin = (isset($array["is_admin"])) ? $array["is_admin"] : 0;
    }

    public function Login($email, $password){
        $this->Connect();

        $email = str_replace(['\'', '"', '--'], '', $email);
        $password = str_replace(['\'', '"', '--'], '', $password);

        $result = $this->Select(
            [
                "email" => $this->connection->real_escape_string($email),
                "password" => $this->connection->real_escape_string($password)
            ],
            ['id', 'name', 'email', 'is_admin']);

        if(count($result) === 0){
            return false;
        }

        $this->Format($result[0]);

        $_SESSION["logged_in"] = true;
        $_SESSION["user"] = serialize($this);
        return true;

    }

}