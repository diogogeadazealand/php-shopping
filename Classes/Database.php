<?php

require_once("Message.php");

class Database {

    private $host;
    private $user;
    private $password;
    private $name;
    protected $table;
    protected $validation;

    protected $connection;

    public function __get($variable){
        return $$variable;
    }

    public function __construct($user, $password, $name, $host = 'localhost')
    {
        $this->user = $user;
        $this->password = $password;
        $this->name = $name;
        $this->host = $host;

        $this->Setup();
    }

    protected function Setup(){}

    protected function Connect(){
        try{
            $this->connection = mysqli_connect($this->host, $this->user, $this->password, $this->name);

            if($this->connection->connect_errno){
                throw new Exception('Connection to database failed: ' . $this->connection->connect_errno);
            }


        } catch (Exception $e){
            return new Message(false, $e->getMessage());
        }

        return new Message(true);
    }

    protected function Close(){
        $this->connection->close();
    }
    
    public function Get($id, $fields = []){

        $result = $this->Connect();

        if(!$result->success){
            return [];
        }

        $rawQuery = 'SELECT ';
        if(count($fields) === 0){
            $rawQuery .= '* ';
        } else {
            for($i = 0; $i < count($fields); $i++){
                $rawQuery .= ($i === count($fields) -1) ? '%s ' : '%s, '; // the last placeholder should not have a comma
            }
            $fields[] = intval($id);
        }

        $rawQuery .= "FROM $this->table WHERE id = %d;";

        $params = (count($fields) === 0) ? $id : $fields;

        $query = vsprintf($rawQuery, $params);
        $result = $this->connection->query($query)->fetch_assoc();
        $this->Close();
        $this->Format($result);
    }

    public function Select($conditions = [], $fields = []){

        $result = $this->Connect();
        $params = array();

        if(!$result->success){
            return [];
        }

        $rawQuery = 'SELECT ';
        if(count($fields) === 0){
            $rawQuery .= '* ';
        } else {
            for($i = 0; $i < count($fields); $i++){
                $rawQuery .= ($i === count($fields) -1) ? '%s ' : '%s, '; // the last placeholder should not have a comma
            }
            $params = $fields;
        }

        $rawQuery .= "FROM $this->table";

        if(count($conditions) === 0){
            $rawQuery .= ';';
        } else {
            $rawQuery .= ' WHERE';
            $statement = '';
            $values = array();

            foreach($conditions as $field => $value){

                switch(gettype($value) ){
                    case 'string': 
                        $rawQuery .= "$statement $field = '%s'"; // the last placeholder should not have a comma
                        break;
                    case 'integer': 
                        $rawQuery .= "$statement $field = %d"; // the last placeholder should not have a comma
                        break;
                    case 'double':
                        $rawQuery .= "$statement $field = %f"; // the last placeholder should not have a comma
                        break;
                    }
                $statement = ' AND';
                $values[] = $value;
            }


            $params = array_merge($params, $values);
        }
        $rawQuery .= ';';

        $query = vsprintf($rawQuery, $params);
        $result = $this->connection->query($query);
        $this->Close();

        if($result === false) return array();

        return $result->fetch_all(MYSQLI_ASSOC);//associative mode
    }

    public function Insert($data = array()){

        $this->Connect();
        $fields = "";
        $values = "";

        if(count($data) === 0){
            $vars = $this->getColumns();
            unset($vars['id']);
  
            $comma = '';
            foreach($vars as $field => $value){
                
                $fields .= "$comma $field";
                switch(gettype($value)){
                    case "string": 
                        $values .= "$comma '%s'";
                        break;
                    case "integer": 
                        $values .= "$comma %d";
                        break;
                    case "double": 
                        $values .= "$comma %f";
                        break;
                }
                $comma = ',';
            }

            $query = "INSERT INTO $this->table($fields) values ($values);";
            $query = vsprintf($query, $vars);
        }

        $result = $this->connection->query($query);
        $this->Close(); 

        return $result;
    
    }

    public function Delete($data = array()){

        if(count($data) <= 0){

            $id = intval($this->getColumns()['id']); //this is supposed to throw an error if an invalid id is provided (like in a sql injection)
            
            if($id === -1) {
                throw new Exception("Please provide and id or list of ids to delete");
            }
            
            $this->Connect();
            $result = $this->connection->query("DELETE FROM $this->table WHERE id = $id");

            return new Message($result);

        }

    }

    ///
    /// Due to a miscalculation, this function had to be implemented being sql injectable.
    ///
    public function Update($data = array()){

        $query = "UPDATE $this->table SET ";
        $values = array();

        if(count($data) === 0){
            $vars = $this->getColumns();
            unset($vars['id']);
  
            $comma = '';
            foreach($vars as $field => $value){
                
                if($field == 'id') continue;

                $query .= "$comma $field = ";
                switch(gettype($value)){
                    case "string": 
                        $query .= "'%s'";
                        break;
                    case "integer": 
                        $query .= "%d";
                        break;
                    case "double": 
                        $query .= " %f";
                        break;
                }
                $values[] = $value;
                $comma = ',';
            }
            $values[] = $this->id;
            $query .= " WHERE id = %d";

            $query = vsprintf($query, $values);
        }

        $this->Connect();
        $this->connection->query($query);
        $result = $this->connection->affected_rows > 0 ? true : false;
        $this->Close();

        return $result;
    }

    ///
    /// Takes and associative array and defines the object properties to the array's values
    ///
    public function Format($array){
        throw new Exception('Method "Format" is not implemented in this class');
    }

    ///
    /// Returns the table model columns
    /// Return: associative array
    ///
    protected function getColumns(){
        $vars = new class{
            public function getPublic($object){
                return get_object_vars($object);
            }
        };
        return $vars->getPublic($this);
    }

    ///
    /// Validates the fields of the current table model
    /// Return: true if validation goes right, Message object if it goes wrong
    ///
    public function Validate(){
        $columns = $this->getColumns();

        $result = new Message(true);
        foreach($columns as $column_name => $value){

            if(!isset($this->validation[$column_name]) || $column_name == "id") continue;

            try{
                switch($this->validation[$column_name]){
                    case Validator::ID:
                        $this->$column_name = intval($value);
                        break;
                    case Validator::NUMBER:
                        $this->$column_name = floatval($value);
                        break;
                }
            } catch(Exception $e){
                trigger_error($e->getMessage(), E_USER_WARNING);
            }

            if(preg_match($this->validation[$column_name], $value) !== 1){
                $result->SetSuccess(false);
                $result->AddText("$column_name is not valid");
            }
        }

        return $result;
    }
}