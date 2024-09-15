<?php
class DBController
{

    private $host = "localhost";
    private $user = "root";
    private $password = "mysql";
    private $database = "newproject";
    private $conn;

    public $secret = '1387961120cb9180e2de5b7409ce2da4f0ae2a905eae683dee57f92f9a27dd2c';

    function __construct()
    {
        $this->conn = $this->connectDB();
    }

    function connectDB()
    {
        $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        $conn->set_charset("utf8");
        return $conn;
    }

    function runQuery($query)
    {
        $result = mysqli_query($this->conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        if (!empty($resultset))
            return $resultset;
    }

    function runInsertQuery($query)
    {
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            return $this->conn->insert_id;
        } else {
            return 0;
        }
    }

    function rnUpdateQuery($query)
    {
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    function rnDeleteQuery($query)
    {
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
?>