<?php

namespace App\Libraries;

class LibDb {

    private $host = '';
    private $user = '';
    private $password = '';

    private $db_name = '';

    private $connection = null;

    private $select;
    private $from;
    private $where;
    private $limit;

    private $query;

    public function __construct($config)
    {
        $this->host = $config['host'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->db_name = $config['db_name'];

        $this->dbConnect();

        if (!$this->connection) return;

        if ($this->db_name){
            $this->dbSelect();
        }
    }

    public function clear()
    {
        $this->select = '';
        $this->from = '';
        $this->where = '';
        $this->limit = '';
    }

    public function select($param) : LibDb
    {
        $this->clear();
        $this->select = $param;
        return $this;
    }

    public function from($param) : LibDb
    {
        $this->from = $param;
        return $this;
    }

    public function where($param) : LibDb
    {
        $this->where = $param;
        return $this;
    }

    public function limit($param) : LibDb
    {
        $this->limit = $param;
        return $this;
    }

    public function insert($tableName, $cols, $values)
    {
        $this->query = "INSERT INTO $tableName ";

        if ($cols){

            if (is_array($cols)){
                $cols = implode(',', $cols);
            }

            $this->query .= " ($cols)";
        }

        if (is_array($values)){
            $values = implode(',', $values);
        }

        $this->query .= " VALUES ($values)";

        return $this->query($this->query);
    }

    public function update($tableName, $params, $where)
    {
        $this->query = "UPDATE $tableName ";

        $update = [];
        foreach ($params as $key => $value){
            $update[] = "$key = '$value'";
        }
        $update = implode(',', $update);

        $this->query .= "SET $update WHERE $where";

        return $this->query($this->query);
    }

    public function find() : ?array
    {
        $this->buildQuery();
        return $this->fetchQuery($this->query);
    }

    public function findOne()
    {
        $this->limit = 1;
        $this->buildQuery();

        return $this->fetchQuery($this->query)[0];
    }

    public function showQuery()
    {
        $this->buildQuery();
        return $this->query;
    }

    private function buildQuery()
    {
        if ($this->from){
            $this->query = ' SELECT ' . $this->select;
        }

        if ($this->from){
            $this->query .= ' FROM ' . $this->from;
        }

        if ($this->where){
            $this->query .= ' WHERE ' . $this->where;
        }

        if ($this->limit){
            $this->query .= ' LIMIT ' . $this->limit;
        }
    }



    private function dbConnect()
    {
        $conn = new \mysqli($this->host, $this->user, $this->password);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $conn->set_charset("utf8");

        $this->connection = $conn;
    }

    private function dbSelect()
    {
        mysqli_select_db($this->connection, $this->db_name);
    }

    public function dbClose()
    {
        $this->connection->close();
    }

    public function query($query)
    {
        return mysqli_query($this->connection, $query);
    }

    public function fetchQuery($query) : array
    {
        $data = $this->query($query);

        $result = [];

        foreach ($data as $row){
            $result[] = $row;
        }

        return $result;
    }

    public function dbShowDatabases()
    {
        $sql = 'SHOW DATABASES';
        $result = $this->fetchQuery($sql);

        echo '<b>Current databases:</b> <br/>';
        foreach($result as $row){
            echo ($row['Database'] . '<br/>');
        }
    }


}