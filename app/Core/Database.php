<?php
// GONATO-Personalized-Fashion-Guidance-/Core/Database.php
class Database {
    private static $host = "localhost";
    private static $db_name = "Amarelle";
    private static $username = "root";
    private static $password = "";
    private static $port = 3307;
    private static $conn;

    public static function connect() {
        if (!self::$conn) {
            self::$conn = new mysqli(self::$host, self::$username, self::$password, self::$db_name, self:: $port);
            if (self::$conn->connect_error) {
                die("Database connection failed: " . self::$conn->connect_error);
            }
        }
        return self::$conn;
    }
}