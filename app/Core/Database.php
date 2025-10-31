<?php
// GONATO-Personalized-Fashion-Guidance-/Core/Database.php
class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "root";
    private $dbname   = "amarelle";
    private $port     = 3307;
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect(
            $this->servername,
            $this->username,
            $this->password,
            $this->dbname,
            $this->port
        );

        if (!$this->conn) {
            die("<div class='status error'>Connection failed: " . mysqli_connect_error() . "</div>");
        } else {
            echo "<div class='status success'>Database connected successfully!</div>";
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}