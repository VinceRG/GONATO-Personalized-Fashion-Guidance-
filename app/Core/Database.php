<?php
// GONATO-Personalized-Fashion-Guidance-/Core/Database.php
class Database {
    private static $servername = "localhost";
    private static $username = "root";
    private static $password = "root";
    private static $dbname   = "amarelle";
    private static $port     = 3307;

    public static function connect() {
        $conn = mysqli_connect(
            self::$servername,
            self::$username,
            self::$password,
            self::$dbname,
            self::$port
        );

        if (!$conn) {
            die("<div class='status error'>Connection failed: " . mysqli_connect_error() . "</div>");
        }
        // Optional: comment this line out in production
        // echo "<div class='status success'>Database connected successfully!</div>";

        return $conn;
    }
}
