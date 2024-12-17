<?php
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $servername = "srv1632.hstgr.io";
        $username = "u143688490_magx"; // Your database username
        $password = "MotoMagX_09"; // Your database password
        $dbname = "u143688490_magx_db"; // Your database name
        
        // Create connection
        $this->connection = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}
?>
